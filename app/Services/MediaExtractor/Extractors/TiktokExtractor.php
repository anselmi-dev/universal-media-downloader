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

        // 1. yt-dlp first when available: official CDN with true 720p/1080p ladders
        $items = $this->extractViaYtDlp($url);
        if (! empty($items)) {
            return $items;
        }

        // 2. Aweme API (often empty since TikTok added signature checks)
        $data = $this->fetchViaAwemeApi($videoId);
        if ($data !== null) {
            return $this->parseMediaItems($data);
        }

        // 3. TikWM with HD request
        $items = $this->fetchViaTikWm($url);
        if (! empty($items)) {
            return $items;
        }

        // 4. TikMate (third-party; no watermark)
        $items = $this->fetchViaTikMate($url);
        if (! empty($items)) {
            return $items;
        }

        // 5. Last resort: scrape page HTML
        $html = $this->fetchPage($url);
        $data = $this->extractRehydrationData($html, $videoId);

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
        $videoInfo = $aweme['video'] ?? [];
        if (empty($videoInfo)) {
            return [];
        }

        $coverUrl = $this->getCoverUrl($videoInfo);
        $qualityUrls = $this->collectQualityUrls($videoInfo);

        $mediaItems = [];
        foreach ($qualityUrls as $entry) {
            $mediaItems[] = new MediaItem(
                url: $entry['url'],
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $coverUrl,
                quality: $entry['quality'],
                filename: $entry['filename'],
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

    /**
     * Collect distinct video URLs ordered from highest quality to lowest.
     *
     * @return list<array{url: string, quality: string, filename: string}>
     */
    private function collectQualityUrls(array $videoInfo): array
    {
        $byHeight = [];

        foreach ($videoInfo['bitrateInfo'] ?? $videoInfo['bit_rate'] ?? [] as $info) {
            $playAddr = $info['PlayAddr'] ?? $info['play_addr'] ?? [];
            $urlList = $playAddr['UrlList'] ?? $playAddr['url_list'] ?? [];
            $url = $urlList[0] ?? null;
            if (! $url || ! $this->isValidVideoHost($url)) {
                continue;
            }

            $height = (int) ($info['Height'] ?? $info['height'] ?? $playAddr['Height'] ?? $playAddr['height'] ?? 0);
            $width = (int) ($info['Width'] ?? $info['width'] ?? $playAddr['Width'] ?? $playAddr['width'] ?? 0);
            $quality = ($height > 0 && $width > 0) ? min($height, $width) : max($height, $width);

            $gearName = (string) ($info['GearName'] ?? $info['gear_name'] ?? '');
            if (preg_match('/(\d+)p/i', $gearName, $m)) {
                $quality = (int) $m[1];
            }

            if ($quality <= 0) {
                continue;
            }

            if (! isset($byHeight[$quality])) {
                $byHeight[$quality] = $this->ensureHttps($url);
            }
        }

        if (! empty($byHeight)) {
            krsort($byHeight);
            $items = [];
            foreach ($byHeight as $quality => $url) {
                $items[] = [
                    'url' => $url,
                    'quality' => $quality.'p',
                    'filename' => 'tiktok-video-'.$quality.'p',
                ];
            }

            return $items;
        }

        $fallbackUrl = $this->getBestVideoUrl($videoInfo);
        if ($fallbackUrl === null) {
            return [];
        }

        return [[
            'url' => $fallbackUrl,
            'quality' => __('tiktok_video_no_watermark'),
            'filename' => 'tiktok-video',
        ]];
    }

    private function getBestVideoUrl(array $videoInfo): ?string
    {
        // Prefer API/download addresses that usually carry the full-resolution file
        foreach ([
            $videoInfo['download_addr'] ?? null,
            $videoInfo['downloadAddr'] ?? null,
            $videoInfo['play_addr_bytevc1'] ?? null,
            $videoInfo['play_addr_h264'] ?? null,
            $videoInfo['play_addr'] ?? null,
        ] as $playAddr) {
            if (! is_array($playAddr)) {
                continue;
            }

            $urlList = $playAddr['url_list'] ?? $playAddr['UrlList'] ?? [];
            $url = $urlList[0] ?? null;
            if ($url && $this->isValidVideoHost($url)) {
                return $this->ensureHttps($url);
            }
        }

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

        return null;
    }

    private function isValidVideoHost(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';

        return str_contains($host, 'tiktokcdn.com')
            || str_contains($host, 'tiktokcdn-us.com')
            || str_contains($host, 'tokcdn.com')
            || str_contains($host, 'tiktok.com')
            || str_contains($host, 'tikwm.com')
            || str_contains($host, 'tikmate.app')
            || str_contains($host, 'nowmvideo.com');
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
        // hd=1 is required for TikWM to populate hdplay (true higher-quality stream)
        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => self::USER_AGENT])
            ->get('https://www.tikwm.com/api/', ['url' => $url, 'hd' => 1]);

        if (! $response->successful()) {
            return [];
        }

        $data = $response->json();
        if (! $data || ($data['code'] ?? -1) !== 0) {
            return [];
        }

        $video = $data['data'] ?? [];
        $coverUrl = $video['cover'] ?? $video['origin_cover'] ?? null;
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

        // HD first so the best option is the default/top choice in the UI
        if (! empty($video['hdplay'])) {
            $items[] = new MediaItem(
                url: $this->ensureHttps($video['hdplay']),
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $coverUrl,
                quality: __('tiktok_video_hd'),
                filename: 'tiktok-video-hd',
            );
        }

        if (! empty($video['play']) && ($video['play'] !== ($video['hdplay'] ?? null))) {
            $items[] = new MediaItem(
                url: $this->ensureHttps($video['play']),
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $coverUrl,
                quality: __('tiktok_video_no_watermark'),
                filename: 'tiktok-video',
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
     * Fetch video via TikMate API (https://api.tikmate.app/api/lookup).
     * Returns array of MediaItem or empty array on failure.
     */
    private function fetchViaTikMate(string $url): array
    {
        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => self::USER_AGENT])
            ->asForm()
            ->post('https://api.tikmate.app/api/lookup', ['url' => $url]);

        if (! $response->successful()) {
            return [];
        }

        $data = $response->json();
        if (! ($data['success'] ?? false) || empty($data['token']) || empty($data['id'])) {
            return [];
        }

        $token = $data['token'];
        $id = $data['id'];
        $coverUrl = $data['cover'] ?? $data['dynamic_cover'] ?? null;
        $downloadBase = 'https://tikmate.app/download/'.$token.'/'.$id.'.mp4';

        return [
            new MediaItem(
                url: $downloadBase.'?hd=1',
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $coverUrl,
                quality: __('tiktok_video_hd'),
                filename: 'tiktok-video-hd',
            ),
            new MediaItem(
                url: $downloadBase,
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $coverUrl,
                quality: __('tiktok_video_no_watermark'),
                filename: 'tiktok-video',
            ),
        ];
    }

    /**
     * Extract video URLs via yt-dlp (multiple resolutions when available).
     * Returns array of MediaItem or empty array if yt-dlp is not available or fails.
     */
    private function extractViaYtDlp(string $url): array
    {
        $ytDlp = $this->findYtDlp();
        if ($ytDlp === null) {
            return [];
        }

        // Webpage rehydration is often captcha-blocked; force the mobile API hostname.
        $result = Process::timeout(40)->run([
            $ytDlp,
            '-J',
            '--no-warnings',
            '--no-playlist',
            '--extractor-args',
            'tiktok:api_hostname=api16-normal-c-useast1a.tiktokv.com',
            $url,
        ]);

        if (! $result->successful()) {
            return [];
        }

        $json = json_decode($result->output(), true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($json)) {
            return [];
        }

        $thumbnailUrl = $json['thumbnail'] ?? null;
        $items = $this->mediaItemsFromYtDlpFormats($json['formats'] ?? [], $thumbnailUrl);

        if (! empty($items)) {
            return $items;
        }

        $videoUrl = $json['url'] ?? null;
        if (empty($videoUrl) || ! $this->isValidVideoHost($videoUrl)) {
            return [];
        }

        return [
            new MediaItem(
                url: $this->ensureHttps($videoUrl),
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $thumbnailUrl,
                quality: __('tiktok_video_hd'),
                filename: 'tiktok-video',
            ),
        ];
    }

    /**
     * Build one MediaItem per distinct quality tier, highest first.
     * Prefers H.264 over H.265 at the same tier for broader playback support.
     *
     * @param  list<array<string, mixed>>  $formats
     * @return list<MediaItem>
     */
    private function mediaItemsFromYtDlpFormats(array $formats, ?string $thumbnailUrl): array
    {
        $bestByQuality = [];

        foreach ($formats as $format) {
            $videoUrl = $format['url'] ?? null;
            $quality = $this->ytDlpQualityLabel($format);
            $vcodec = (string) ($format['vcodec'] ?? 'none');

            if ($quality < 240 || $vcodec === 'none' || empty($videoUrl) || ! $this->isValidVideoHost($videoUrl)) {
                continue;
            }

            $current = $bestByQuality[$quality] ?? null;
            if ($current === null || $this->ytDlpFormatScore($format) > $this->ytDlpFormatScore($current)) {
                $bestByQuality[$quality] = $format;
            }
        }

        if (empty($bestByQuality)) {
            return [];
        }

        krsort($bestByQuality);

        $items = [];
        foreach ($bestByQuality as $quality => $format) {
            $items[] = new MediaItem(
                url: $this->ensureHttps($format['url']),
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $thumbnailUrl,
                quality: $quality.'p',
                filename: 'tiktok-video-'.$quality.'p',
            );
        }

        return $items;
    }

    /**
     * Resolve a TikTok "Xp" label. Vertical videos often report height > width
     * (e.g. 1920x1080 for 1080p), so prefer format_id / the shorter side.
     *
     * @param  array<string, mixed>  $format
     */
    private function ytDlpQualityLabel(array $format): int
    {
        $formatId = (string) ($format['format_id'] ?? '');
        if (preg_match('/(\d+)p/i', $formatId, $m)) {
            return (int) $m[1];
        }

        $height = (int) ($format['height'] ?? 0);
        $width = (int) ($format['width'] ?? 0);

        if ($height > 0 && $width > 0) {
            return min($height, $width);
        }

        return max($height, $width);
    }

    /**
     * @param  array<string, mixed>  $format
     */
    private function ytDlpFormatScore(array $format): int
    {
        $vcodec = strtolower((string) ($format['vcodec'] ?? ''));
        $acodec = strtolower((string) ($format['acodec'] ?? 'none'));
        $tbr = (int) ($format['tbr'] ?? $format['vbr'] ?? 0);

        // Prefer progressive (video+audio) and H.264 for compatibility.
        $score = $tbr;
        if ($acodec !== 'none' && $acodec !== '') {
            $score += 100000;
        }
        if (str_contains($vcodec, 'h264') || str_contains($vcodec, 'avc')) {
            $score += 50000;
        }

        return $score;
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

}
