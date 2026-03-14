<?php

namespace App\Services\MediaExtractor\Extractors;

use App\DTOs\MediaItem;
use App\Services\MediaExtractor\Contracts\ExtractorInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use InvalidArgumentException;
use RuntimeException;

class TiktokExtractor implements ExtractorInterface
{
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    private const APP_USER_AGENT = 'com.zhiliaoapp.musically/2023501030 (Linux; U; Android 14; en_US; Pixel 8 Pro; Build/TP1A.220624.014; Cronet/58.0.2991.0)';

    private const AWEME_API = 'https://api16-normal-c-useast1a.tiktokv.com/aweme/v1/multi/aweme/detail/';

    public function extract(string $url): array
    {
        $url = $this->resolveShortUrl($url);
        $videoId = $this->extractVideoId($url);

        // 1. Try aweme API first (often returns empty since TikTok added signature checks)
        $data = $this->fetchViaAwemeApi($videoId);

        if ($data === null) {
            // 2. TikWM API (third-party, reliable, no installation needed)
            $items = $this->fetchViaTikWm($url);
            if (! empty($items)) {
                return $items;
            }

            // 3. Fallback: scrape page (often blocked by TikTok "Please wait" challenge)
            try {
                $html = $this->fetchPage($url);
                $data = $this->extractRehydrationData($html, $videoId);
            } catch (RuntimeException $e) {
                // 4. Last resort: yt-dlp if installed
                $items = $this->extractViaYtDlp($url);
                if (! empty($items)) {
                    return $items;
                }
                throw $e;
            }
        }

        return $this->parseMediaItems($data);
    }

    public function getPlatformName(): string
    {
        return 'TikTok';
    }

    public static function supports(string $url): bool
    {
        return (bool) preg_match('#(tiktok\.com|vm\.tiktok\.com)#i', $url);
    }

    private function resolveShortUrl(string $url, int $depth = 0): string
    {
        if (! str_contains($url, 'vm.tiktok.com') || $depth > 5) {
            return $url;
        }

        $response = Http::withHeaders(['User-Agent' => self::USER_AGENT])
            ->withoutRedirecting()
            ->get($url);

        $location = $response->header('Location');

        if ($location) {
            $next = str_starts_with($location, 'http') ? $location : 'https://www.tiktok.com'.$location;

            return $this->resolveShortUrl($next, $depth + 1);
        }

        return $url;
    }

    private function extractVideoId(string $url): string
    {
        // Supports /video/123 and /photo/123 (slider/carousel)
        if (preg_match('#/(?:video|photo)/(\d+)#', $url, $m)) {
            return $m[1];
        }

        throw new InvalidArgumentException(__('errors.tiktok_invalid_url'));
    }

    /**
     * Fetch video data via TikTok's mobile app API (aweme/detail).
     * Returns aweme_detail or null if the API fails (e.g. blocked).
     */
    private function fetchViaAwemeApi(string $videoId): ?array
    {
        $deviceId = (string) random_int(7200000000000000000, 7325099899999999999);

        $response = Http::withHeaders([
            'User-Agent' => self::APP_USER_AGENT,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
            'x-argus' => '',
        ])->asForm()->post(self::AWEME_API, [
            'aweme_ids' => '['.$videoId.']',
            'request_source' => '0',
        ], [
            'device_id' => $deviceId,
            'aid' => '1233',
            'channel' => 'googleplay',
            'app_name' => 'musical_ly',
            'version_code' => '350103',
            'version_name' => '35.1.3',
            'device_platform' => 'android',
            'os' => 'android',
            'device_type' => 'Pixel 8 Pro',
            'os_version' => '14',
        ]);

        if (! $response->successful()) {
            return null;
        }

        $json = $response->json();

        // API may return status_code when blocked
        if (($json['status_code'] ?? 0) !== 0) {
            return null;
        }

        $awemeList = $json['aweme_details'] ?? $json['aweme_list'] ?? [];

        if (empty($awemeList)) {
            return null;
        }

        $aweme = $awemeList[0] ?? null;

        if (! $aweme || empty($aweme['video'] ?? [])) {
            return null;
        }

        return $aweme;
    }

    private function fetchPage(string $url): string
    {
        $response = Http::withHeaders([
            'User-Agent' => self::USER_AGENT,
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->get($url);

        if (! $response->successful()) {
            throw new RuntimeException(__('errors.tiktok_page_failed'));
        }

        return $response->body();
    }

    private function extractRehydrationData(string $html, string $videoId): array
    {
        if (! preg_match('#<script[^>]+id="__UNIVERSAL_DATA_FOR_REHYDRATION__"[^>]*>([^<]+)</script>#s', $html, $m)) {
            throw new RuntimeException(__('errors.tiktok_extract_failed'));
        }

        $json = json_decode($m[1], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(__('errors.tiktok_parse_failed'));
        }

        $scope = $json['__DEFAULT_SCOPE__'] ?? [];
        $videoDetail = $scope['webapp.video-detail'] ?? null;

        if (! $videoDetail) {
            throw new RuntimeException(__('errors.video_not_found'));
        }

        $statusCode = $videoDetail['statusCode'] ?? 0;
        if ($statusCode === 10202 || $statusCode === 10221) {
            throw new RuntimeException(__('errors.video_private'));
        }

        $itemStruct = $videoDetail['itemInfo']['itemStruct'] ?? null;

        if (! $itemStruct) {
            throw new RuntimeException(__('errors.tiktok_extract_info_failed'));
        }

        return $itemStruct;
    }

    private function parseMediaItems(array $aweme): array
    {
        $mediaItems = [];
        $videoInfo = $aweme['video'] ?? [];

        if (empty($videoInfo)) {
            return $mediaItems;
        }

        $coverUrl = $this->getCoverUrl($videoInfo);
        $videoUrl = $this->getBestVideoUrl($videoInfo);

        if ($videoUrl) {
            $mediaItems[] = new MediaItem(
                url: $videoUrl,
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $coverUrl,
            );
        }

        return $mediaItems;
    }

    private function getCoverUrl(array $videoInfo): ?string
    {
        foreach (['cover', 'origin_cover', 'dynamic_cover'] as $key) {
            $list = $videoInfo[$key]['url_list'] ?? [];
            if (! empty($list)) {
                return $list[0];
            }
        }

        return null;
    }

    private function getBestVideoUrl(array $videoInfo): ?string
    {
        // playAddr (web format) - can be object with src or array of {src}
        $playAddr = $videoInfo['playAddr'] ?? null;
        if ($playAddr !== null) {
            $sources = isset($playAddr['src'])
                ? [$playAddr['src']]
                : array_filter(array_map(fn ($x) => $x['src'] ?? null, is_array($playAddr) ? $playAddr : [$playAddr]));

            foreach ($sources as $url) {
                if ($url && $this->isValidVideoHost($url)) {
                    return $this->ensureHttps($url);
                }
            }
        }

        // bitrateInfo (web format) - pick highest quality
        $bitrateInfo = $videoInfo['bitrateInfo'] ?? [];
        $bestUrl = null;
        $bestWidth = 0;

        foreach ($bitrateInfo as $info) {
            $playAddr = $info['PlayAddr'] ?? [];
            $urlList = $playAddr['UrlList'] ?? $playAddr['url_list'] ?? [];
            $url = $urlList[0] ?? null;

            if ($url && $this->isValidVideoHost($url)) {
                $width = (int) ($info['Bitrate'] ?? $info['width'] ?? 0);
                if ($width > $bestWidth) {
                    $bestWidth = $width;
                    $bestUrl = $url;
                }
            }
        }

        if ($bestUrl) {
            return $this->ensureHttps($bestUrl);
        }

        // play_addr (API format)
        $playAddr = $videoInfo['play_addr'] ?? $videoInfo['play_addr_h264'] ?? $videoInfo['play_addr_bytevc1'] ?? null;
        if ($playAddr) {
            $urlList = $playAddr['url_list'] ?? [];
            $url = $urlList[0] ?? null;

            if ($url && $this->isValidVideoHost($url)) {
                return $this->ensureHttps($url);
            }
        }

        // download_addr (fallback, may be watermarked)
        $downloadAddr = $videoInfo['download_addr'] ?? $videoInfo['downloadAddr'] ?? null;
        if ($downloadAddr) {
            $urlList = $downloadAddr['url_list'] ?? $downloadAddr['UrlList'] ?? $downloadAddr['url_list'] ?? [];
            $url = $urlList[0] ?? null;

            if ($url && $this->isValidVideoHost($url)) {
                return $this->ensureHttps($url);
            }
        }

        return null;
    }

    private function isValidVideoHost(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';

        return str_contains($host, 'tiktokcdn.com')
            || str_contains($host, 'tiktokcdn-us.com')
            || str_contains($host, 'tokcdn.com')
            || str_contains($host, 'tiktok.com')
            || str_contains($host, 'tikwm.com');
    }

    private function ensureHttps(string $url): string
    {
        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }

        return $url;
    }

    /**
     * Fetch video via TikWM API (https://www.tikwm.com/api/).
     * Returns array of MediaItem or empty array on failure.
     */
    private function fetchViaTikWm(string $url): array
    {
        // GET like the official TikWM web client (https://www.tikwm.com/api/?url=...)
        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => self::USER_AGENT])
            ->get('https://www.tikwm.com/api/', ['url' => $url]);

        if (! $response->successful()) {
            return [];
        }

        $data = $response->json();
        if (! $data || ($data['code'] ?? -1) !== 0) {
            return [];
        }

        $video = $data['data'] ?? [];
        $coverUrl = $video['cover'] ?? null;
        $items = [];

        // Photo slider/carousel: images array
        $images = $video['images'] ?? [];
        if (! empty($images)) {
            foreach ($images as $i => $imgUrl) {
                if (! empty($imgUrl)) {
                    $items[] = new MediaItem(
                        url: $this->ensureHttps($imgUrl),
                        type: 'image',
                        platform: $this->getPlatformName(),
                        thumbnailUrl: $imgUrl,
                        quality: count($images) > 1 ? __('tiktok_slide', ['n' => $i + 1, 'total' => count($images)]) : null,
                        filename: 'tiktok-photo-'.($i + 1),
                    );
                }
            }
            // Audio de fondo (opcional para sliders)
            if (! empty($video['music'])) {
                $items[] = new MediaItem(
                    url: $this->ensureHttps($video['music']),
                    type: 'audio',
                    platform: $this->getPlatformName(),
                    thumbnailUrl: $coverUrl,
                    quality: __('tiktok_audio'),
                    filename: 'tiktok-audio',
                );
            }

            return $items;
        }

        // Video: 1. Sin marca de agua, 2. HD, 3. Audio
        if (! empty($video['play'])) {
            $items[] = new MediaItem(
                url: $this->ensureHttps($video['play']),
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $coverUrl,
                quality: __('tiktok_video_no_watermark'),
                filename: 'tiktok-video',
            );
        }

        if (! empty($video['hdplay']) && $video['hdplay'] !== ($video['play'] ?? '')) {
            $items[] = new MediaItem(
                url: $this->ensureHttps($video['hdplay']),
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $coverUrl,
                quality: __('tiktok_video_hd'),
                filename: 'tiktok-video-hd',
            );
        }

        if (! empty($video['music'])) {
            $items[] = new MediaItem(
                url: $this->ensureHttps($video['music']),
                type: 'audio',
                platform: $this->getPlatformName(),
                thumbnailUrl: $coverUrl,
                quality: __('tiktok_audio'),
                filename: 'tiktok-audio',
            );
        }

        return $items;
    }

    /**
     * Extract video URL via yt-dlp when native methods fail.
     * Returns array of MediaItem or empty array if yt-dlp is not available or fails.
     */
    private function extractViaYtDlp(string $url): array
    {
        $ytDlp = $this->findYtDlp();
        if ($ytDlp === null) {
            return [];
        }

        $result = Process::timeout(25)->run([
            $ytDlp,
            '-J',
            '--no-warnings',
            '--no-playlist',
            $url,
        ]);

        if (! $result->successful()) {
            return [];
        }

        $json = json_decode($result->output(), true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($json)) {
            return [];
        }

        $videoUrl = $json['url'] ?? null;
        if (empty($videoUrl) && ! empty($json['formats'])) {
            $best = $this->pickBestFormat($json['formats']);
            $videoUrl = $best['url'] ?? null;
        }

        if (empty($videoUrl) || ! $this->isValidVideoHost($videoUrl)) {
            return [];
        }

        $thumbnailUrl = $json['thumbnail'] ?? null;

        return [
            new MediaItem(
                url: $this->ensureHttps($videoUrl),
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $thumbnailUrl,
            ),
        ];
    }

    private function findYtDlp(): ?string
    {
        $candidates = PHP_OS_FAMILY === 'Windows'
            ? ['yt-dlp.exe', 'yt-dlp']
            : ['yt-dlp'];

        foreach ($candidates as $cmd) {
            $result = Process::run([$cmd, '--version']);
            if ($result->successful()) {
                return $cmd;
            }
        }

        return null;
    }

    private function pickBestFormat(array $formats): ?array
    {
        $videoFormats = array_filter($formats, fn ($f) => ($f['vcodec'] ?? 'none') !== 'none');
        if (empty($videoFormats)) {
            return $formats[0] ?? null;
        }

        usort($videoFormats, function ($a, $b) {
            $heightA = (int) ($a['height'] ?? 0);
            $heightB = (int) ($b['height'] ?? 0);
            if ($heightA !== $heightB) {
                return $heightB <=> $heightA;
            }
            $brA = (int) ($a['tbr'] ?? $a['vbr'] ?? 0);
            $brB = (int) ($b['tbr'] ?? $b['vbr'] ?? 0);

            return $brB <=> $brA;
        });

        return $videoFormats[0] ?? null;
    }
}
