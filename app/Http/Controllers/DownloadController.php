<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

    public function proxy(Request $request): StreamedResponse
    {
        $url = $request->query('url');
        $filename = $request->query('filename', 'download');

        abort_if(empty($url), 400, 'No URL provided.');
        abort_unless($this->isAllowedDomain($url), 403, 'Download not permitted for this domain.');

        $headers = $this->headersForUrl($url);
        $response = Http::withHeaders($headers)->withOptions(['stream' => true])->get($url);

        abort_unless($response->successful(), 502, 'Could not retrieve the media file.');

        $contentType = $response->header('Content-Type') ?? 'application/octet-stream';
        $extension = $this->extensionFromContentType($contentType, $url);
        $downloadName = $filename.'.'.$extension;

        return response()->stream(
            function () use ($response) {
                $body = $response->getBody();
                while (! $body->eof()) {
                    echo $body->read(8192);
                    ob_flush();
                    flush();
                }
            },
            200,
            [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
                'X-Accel-Buffering' => 'no',
            ]
        );
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
                'Referer'    => 'https://www.tiktok.com/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ];
        }

        $isInstagramCdn = str_contains($host, 'cdninstagram')
            || str_contains($host, 'fbcdn')
            || str_ends_with($host, 'instagram.com');

        if ($isInstagramCdn) {
            return [
                'Referer'    => 'https://www.instagram.com/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ];
        }

        return [];
    }

    private function extensionFromContentType(string $contentType, string $url): string
    {
        // Prefer content-type header
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
            default => pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'bin',
        };
    }
}
