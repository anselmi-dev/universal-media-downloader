<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Domains from which proxied downloads are permitted.
     * Extend this list when adding support for new platforms.
     */
    private const ALLOWED_DOMAINS = [
        // X / Twitter
        'pbs.twimg.com',
        'video.twimg.com',
        'ton.twimg.com',
        // TikTok CDN (v77.tiktokcdn.com, v16m-default.tiktokcdn-us.com, etc.)
        'tiktokcdn.com',
        'tiktokcdn-us.com',
        'tokcdn.com',
        // TikTok web video (v19-webapp-prime.tiktok.com, etc.)
        'tiktok.com',
        // TikWM API CDN
        'tikwm.com',
        // Instagram / Facebook CDN (e.g. scontent-lga3-2.cdninstagram.com, video.cdninstagram.com)
        'cdninstagram.com',
        'fbcdn.net',
        // Instagram web
        'instagram.com',
        // Reddit video CDN
        'v.redd.it',
        // Reddit image CDN
        'i.redd.it',
        // Reddit preview CDN
        'preview.redd.it',
        'external-preview.redd.it',
        // Imgur (often linked from Reddit)
        'i.imgur.com',
    ];

    public function proxy(Request $request): BinaryFileResponse|StreamedResponse
    {
        $url = $request->query('url');
        $filename = $request->query('filename', 'download');

        abort_if(empty($url), 400, 'No URL provided.');
        abort_unless($this->isAllowedDomain($url), 403, 'Download not permitted for this domain.');

        // Twitter's progressive MP4s use a non-standard "Twitter-vork" muxer that
        // many players (Windows Photos, Movies & TV, older VLC) cannot play.
        // Remuxing with ffmpeg (copy, no re-encode) fixes playback.
        if ($this->isTwitterVideoUrl($url)) {
            $remuxed = $this->proxyTwitterRemuxed($url, $filename);
            if ($remuxed !== null) {
                return $remuxed;
            }
        }

        return $this->proxyStream($url, $filename);
    }

    private function proxyStream(string $url, string $filename): StreamedResponse
    {
        $headers = $this->headersForUrl($url);
        $response = Http::withHeaders($headers)->withOptions(['stream' => true])->get($url);

        abort_unless($response->successful(), 502, 'Could not retrieve the media file.');

        $contentType = $response->header('Content-Type') ?? 'application/octet-stream';
        $extension = $this->extensionFromContentType($contentType, $url);
        $downloadName = $filename.'.'.$extension;
        $contentLength = $response->header('Content-Length');

        $responseHeaders = [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
            'X-Accel-Buffering' => 'no',
        ];

        if (! empty($contentLength) && ctype_digit((string) $contentLength)) {
            $responseHeaders['Content-Length'] = $contentLength;
        }

        return response()->stream(
            function () use ($response) {
                $body = $response->getBody();
                while (! $body->eof()) {
                    echo $body->read(8192);
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            },
            200,
            $responseHeaders
        );
    }

    /**
     * Download a Twitter video and remux it into a standards-compliant MP4.
     */
    private function proxyTwitterRemuxed(string $url, string $filename): ?BinaryFileResponse
    {
        $ffmpeg = $this->findFfmpeg();
        if ($ffmpeg === null) {
            return null;
        }

        $tmpIn = tempnam(sys_get_temp_dir(), 'tw_in_');
        $tmpMid = tempnam(sys_get_temp_dir(), 'tw_mid_');
        $tmpOut = tempnam(sys_get_temp_dir(), 'tw_out_');

        // tempnam creates files; rename to add extensions ffmpeg expects
        $in = $tmpIn.'.mp4';
        $mid = $tmpMid.'.mkv';
        $out = $tmpOut.'.mp4';
        @unlink($tmpIn);
        @unlink($tmpMid);
        @unlink($tmpOut);

        try {
            $response = Http::withHeaders($this->headersForUrl($url))
                ->withOptions(['sink' => $in])
                ->timeout(120)
                ->get($url);

            if (! $response->successful() || ! is_file($in) || filesize($in) === 0) {
                $this->cleanupTemp([$in, $mid, $out]);

                return null;
            }

            // Double remux (mp4 → mkv → mp4) is required for Twitter-vork containers;
            // a single mp4→mp4 copy is often not enough for Windows/VLC.
            $step1 = Process::timeout(180)->run(array_merge($ffmpeg, [
                '-y', '-hide_banner', '-loglevel', 'error',
                '-i', $in,
                '-c', 'copy',
                '-map', '0',
                $mid,
            ]));

            if (! $step1->successful() || ! is_file($mid)) {
                $this->cleanupTemp([$in, $mid, $out]);

                return null;
            }

            $step2 = Process::timeout(180)->run(array_merge($ffmpeg, [
                '-y', '-hide_banner', '-loglevel', 'error',
                '-i', $mid,
                '-c', 'copy',
                '-map', '0',
                '-movflags', '+faststart',
                $out,
            ]));

            @unlink($in);
            @unlink($mid);

            if (! $step2->successful() || ! is_file($out) || filesize($out) === 0) {
                $this->cleanupTemp([$out]);

                return null;
            }

            $downloadName = $filename.'.mp4';

            return response()
                ->file($out, [
                    'Content-Type' => 'video/mp4',
                    'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
                ])
                ->deleteFileAfterSend(true);
        } catch (\Throwable) {
            $this->cleanupTemp([$in, $mid, $out]);

            return null;
        }
    }

    private function isTwitterVideoUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        return str_contains($host, 'video.twimg.com')
            && (str_contains($path, '.mp4') || str_contains($path, '/vid/'));
    }

    /**
     * @return list<string>|null
     */
    private function findFfmpeg(): ?array
    {
        $candidates = [['ffmpeg'], ['ffmpeg.exe']];

        // Absolute paths help when PHP/Apache has a stripped PATH (common on Laragon).
        if (PHP_OS_FAMILY === 'Windows') {
            foreach ($this->windowsFfmpegPaths() as $absolute) {
                array_unshift($candidates, [$absolute]);
            }
        }

        foreach ($candidates as $cmd) {
            if (Process::run(array_merge($cmd, ['-version']))->successful()) {
                return $cmd;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function windowsFfmpegPaths(): array
    {
        $paths = [];

        $configured = env('FFMPEG_PATH');
        if (is_string($configured) && $configured !== '' && is_file($configured)) {
            $paths[] = $configured;
        }

        $localAppData = getenv('LOCALAPPDATA') ?: '';
        $userProfile = getenv('USERPROFILE') ?: '';
        $programFiles = getenv('ProgramFiles') ?: 'C:\\Program Files';

        foreach ([
            $localAppData.'\\Microsoft\\WinGet\\Links\\ffmpeg.exe',
            $userProfile.'\\AppData\\Local\\Microsoft\\WinGet\\Links\\ffmpeg.exe',
            $programFiles.'\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\laragon\\bin\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\laragon\\bin\\ffmpeg\\ffmpeg.exe',
        ] as $path) {
            if ($path !== '' && is_file($path)) {
                $paths[] = $path;
            }
        }

        // WinGet package install (versioned folder name)
        foreach (array_filter([
            $localAppData.'\\Microsoft\\WinGet\\Packages',
            $userProfile.'\\AppData\\Local\\Microsoft\\WinGet\\Packages',
        ]) as $wingetPackages) {
            if (! is_dir($wingetPackages)) {
                continue;
            }
            foreach (glob($wingetPackages.'\\Gyan.FFmpeg*\\ffmpeg-*\\bin\\ffmpeg.exe') ?: [] as $match) {
                if (is_file($match)) {
                    $paths[] = $match;
                }
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @param  list<string>  $paths
     */
    private function cleanupTemp(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_string($path) && is_file($path)) {
                @unlink($path);
            }
        }
    }

    private function isAllowedDomain(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';

        foreach (self::ALLOWED_DOMAINS as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.'.$allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Headers required by some CDNs (e.g. TikTok) to serve full video instead of audio-only.
     */
    private function headersForUrl(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';

        $isTiktokCdn = str_contains($host, 'tiktokcdn')
            || str_contains($host, 'tokcdn')
            || str_contains($host, 'tikwm');

        if ($isTiktokCdn) {
            return [
                'Referer' => 'https://www.tiktok.com/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ];
        }

        $isInstagramCdn = str_contains($host, 'cdninstagram')
            || str_contains($host, 'fbcdn')
            || str_ends_with($host, 'instagram.com');

        if ($isInstagramCdn) {
            return [
                'Referer' => 'https://www.instagram.com/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ];
        }

        if (str_contains($host, 'twimg.com')) {
            return [
                'Referer' => 'https://x.com/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ];
        }

        return [];
    }

    private function extensionFromContentType(string $contentType, string $url): string
    {
        return match (true) {
            str_contains($contentType, 'jpeg') => 'jpg',
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'gif') => 'gif',
            str_contains($contentType, 'webp') => 'webp',
            str_contains($contentType, 'mp4') => 'mp4',
            str_contains($contentType, 'webm') => 'webm',
            str_contains($contentType, 'mp3') => 'mp3',
            str_contains($contentType, 'mpeg') => 'mp3',
            str_contains($contentType, 'm4a') => 'm4a',
            str_contains($contentType, 'audio') => 'm4a',
            default => pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'bin',
        };
    }
}
