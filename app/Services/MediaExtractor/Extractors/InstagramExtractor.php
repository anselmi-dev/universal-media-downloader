<?php

declare(strict_types=1);

namespace App\Services\MediaExtractor\Extractors;

use App\DTOs\MediaItem;
use App\Services\MediaExtractor\Contracts\ExtractorInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class InstagramExtractor implements ExtractorInterface
{
    private const USER_AGENT_WEB = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36';

    /**
     * Mobile app User-Agent accepted by Instagram's private API.
     */
    private const USER_AGENT_APP = 'Instagram 195.0.0.31.123 Android (29/10; 480dpi; 1080x2032; HUAWEI; ANE-LX1; HWANE; en_US; 302733750)';

    private const IG_APP_ID = '936619743392459';

    /** PolarisPostRootQuery – returns xdt_api__v1__media__shortcode__web_info */
    private const DOC_ID = '27128499623469141';

    private const GRAPHQL_URL = 'https://www.instagram.com/graphql/query';

    private bool $sessionRejected = false;

    // -------------------------------------------------------------------------
    // Public interface
    // -------------------------------------------------------------------------

    public function extract(string $url): array
    {
        $shortcode = $this->extractShortcode($url);
        $isStory = $this->isStoryUrl($url);
        $sessionId = $this->getSessionId();

        if ($isStory && $sessionId !== null) {
            $items = $this->isHighlightUrl($url)
                ? $this->fetchHighlightItems($shortcode, $sessionId)
                : $this->fetchStoryItems($url, $sessionId);

            if (! empty($items)) {
                return $items;
            }
        }

        if (! $isStory) {
            if ($sessionId !== null && ! $this->sessionRejected) {
                $items = $this->fetchPostViaMobileApi($shortcode, $sessionId);
                if (! empty($items)) {
                    return $items;
                }

                $items = $this->fetchPostViaAuthApi($shortcode, $sessionId);
                if (! empty($items)) {
                    return $items;
                }
            }

            $items = $this->fetchViaGraphQL($shortcode);
            if (! empty($items)) {
                return $items;
            }
        }

        $items = $this->extractViaYtDlp($url, $this->sessionRejected ? null : $sessionId);
        if (! empty($items)) {
            return $items;
        }

        if ($sessionId !== null && ! $this->sessionRejected) {
            $items = $this->extractViaYtDlp($url, null);
            if (! empty($items)) {
                return $items;
            }
        }

        if ($isStory) {
            if ($sessionId === null) {
                throw new RuntimeException(__('errors.instagram_story_no_session'));
            }

            throw new RuntimeException(__('errors.instagram_story_requires_auth'));
        }

        if ($sessionId !== null && $this->sessionRejected) {
            throw new RuntimeException(__('errors.instagram_session_expired'));
        }

        throw new RuntimeException(__('errors.instagram_extract_failed'));
    }

    public function getPlatformName(): string
    {
        return 'Instagram';
    }

    public static function supports(string $url): bool
    {
        return (bool) preg_match(
            '#instagram\.com/(?:p|reel|tv|stories/highlights|stories/[\w.]+)/[\w-]+#i',
            $url
        );
    }

    // -------------------------------------------------------------------------
    // URL helpers
    // -------------------------------------------------------------------------

    private function isStoryUrl(string $url): bool
    {
        return (bool) preg_match('#instagram\.com/stories/#i', $url);
    }

    private function isHighlightUrl(string $url): bool
    {
        return (bool) preg_match('#instagram\.com/stories/highlights/#i', $url);
    }

    /**
     * Extract the post shortcode or highlight/story ID from any IG URL.
     */
    private function extractShortcode(string $url): string
    {
        if (preg_match('#instagram\.com/stories/highlights/([\w-]+)#i', $url, $m)) {
            return $m[1];
        }

        if (preg_match('#instagram\.com/stories/[\w.]+/([\w-]+)#i', $url, $m)) {
            return $m[1];
        }

        if (preg_match('#instagram\.com/(?:p|reel|tv)/([\w-]+)#i', $url, $m)) {
            return $m[1];
        }

        throw new InvalidArgumentException(__('errors.instagram_invalid_url'));
    }

    private function getSessionId(): ?string
    {
        $id = config('services.instagram.session_id');

        if (! is_string($id) || $id === '') {
            return null;
        }

        return urldecode($id);
    }

    /**
     * Convert an Instagram shortcode to its numeric media ID.
     */
    private function shortcodeToMediaId(string $code): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
        $id = '0';

        for ($i = 0, $len = strlen($code); $i < $len; $i++) {
            $pos = strpos($alphabet, $code[$i]);
            if ($pos === false) {
                throw new InvalidArgumentException(__('errors.instagram_invalid_url'));
            }

            $id = bcmul($id, '64');
            $id = bcadd($id, (string) $pos);
        }

        return $id;
    }

    // -------------------------------------------------------------------------
    // Authenticated Instagram API (requires INSTAGRAM_SESSION_ID)
    // -------------------------------------------------------------------------

    private function fetchHighlightItems(string $highlightId, string $sessionId): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders($this->authHeaders($sessionId))
                ->withOptions(['allow_redirects' => ['max' => 3]])
                ->get('https://i.instagram.com/api/v1/feed/reels_media/', [
                    'reel_ids' => "highlight:{$highlightId}",
                ]);
        } catch (Throwable) {
            $this->sessionRejected = true;

            return [];
        }

        if ($this->isSessionRejectedResponse($response)) {
            return [];
        }

        if (! $response->ok()) {
            return [];
        }

        return $this->parseReelsMediaResponse($response->json() ?? []);
    }

    private function fetchStoryItems(string $storyUrl, string $sessionId): array
    {
        if (! preg_match('#instagram\.com/stories/[\w.]+/([\d]+)#i', $storyUrl, $m)) {
            return [];
        }

        return $this->fetchMediaInfoItems($m[1], $sessionId, 'instagram-story');
    }

    /**
     * Fetch a regular post/reel via the authenticated mobile API.
     */
    private function fetchPostViaMobileApi(string $shortcode, string $sessionId): array
    {
        try {
            $mediaId = $this->shortcodeToMediaId($shortcode);
        } catch (InvalidArgumentException) {
            return [];
        }

        return $this->fetchMediaInfoItems($mediaId, $sessionId, 'instagram');
    }

    private function fetchMediaInfoItems(string $mediaId, string $sessionId, string $filenamePrefix): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders($this->authHeaders($sessionId))
                ->withOptions(['allow_redirects' => ['max' => 3]])
                ->get("https://i.instagram.com/api/v1/media/{$mediaId}/info/");
        } catch (Throwable) {
            $this->sessionRejected = true;

            return [];
        }

        if ($this->isSessionRejectedResponse($response)) {
            return [];
        }

        if (! $response->ok()) {
            return [];
        }

        $items = $response->json('items') ?? [];
        if (! is_array($items) || $items === []) {
            return [];
        }

        return $this->parseMobileApiItems($items, $filenamePrefix);
    }

    private function parseReelsMediaResponse(array $json): array
    {
        $items = [];

        foreach ($json['reels'] ?? [] as $reel) {
            $reelItems = $reel['items'] ?? [];
            if (! is_array($reelItems) || $reelItems === []) {
                continue;
            }

            foreach ($this->parseMobileApiItems($reelItems, 'instagram-story') as $item) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Fetch a post/reel via authenticated GraphQL (PolarisPostRootQuery).
     */
    private function fetchPostViaAuthApi(string $shortcode, string $sessionId): array
    {
        return $this->requestGraphQL($shortcode, $sessionId);
    }

    private function authHeaders(string $sessionId): array
    {
        return [
            'User-Agent' => self::USER_AGENT_APP,
            'X-IG-App-ID' => self::IG_APP_ID,
            'Accept-Language' => 'en-US',
            'Accept-Encoding' => 'gzip, deflate',
            'Cookie' => "sessionid={$sessionId}; ig_did=UNKNOWN",
            'X-IG-Connection-Type' => 'WIFI',
        ];
    }

    private function isSessionRejectedResponse(Response $response): bool
    {
        if (in_array($response->status(), [301, 302, 303, 307, 308], true)) {
            $this->sessionRejected = true;

            return true;
        }

        $message = (string) ($response->json('message') ?? '');
        if ($response->status() === 403 || str_contains(strtolower($message), 'login_required')) {
            $this->sessionRejected = true;

            return true;
        }

        return false;
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return list<MediaItem>
     */
    private function parseMobileApiItems(array $items, string $filenamePrefix): array
    {
        $parsed = [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            // Carousel / sidecar
            if ((int) ($item['media_type'] ?? 0) === 8 && ! empty($item['carousel_media'])) {
                $carousel = $item['carousel_media'];
                $total = count($carousel);
                foreach ($carousel as $i => $slide) {
                    if (! is_array($slide)) {
                        continue;
                    }
                    $media = $this->parseMobileApiItem($slide, (int) $i, $total, $filenamePrefix);
                    if ($media) {
                        $parsed[] = $media;
                    }
                }

                continue;
            }

            $media = $this->parseMobileApiItem($item, (int) $index, count($items), $filenamePrefix);
            if ($media) {
                $parsed[] = $media;
            }
        }

        return $parsed;
    }

    private function parseMobileApiItem(array $item, int $index, int $total, string $filenamePrefix): ?MediaItem
    {
        $thumbnail = $item['image_versions2']['candidates'][0]['url'] ?? null;
        $quality = $total > 1 ? __('instagram_slide', ['n' => $index + 1, 'total' => $total]) : null;

        if (! empty($item['video_versions']) && is_array($item['video_versions'])) {
            $versions = $item['video_versions'];
            usort($versions, fn ($a, $b) => ($b['width'] ?? 0) <=> ($a['width'] ?? 0));
            $url = $versions[0]['url'] ?? null;
            if (is_string($url) && $url !== '') {
                return new MediaItem(
                    url: $url,
                    type: 'video',
                    platform: $this->getPlatformName(),
                    thumbnailUrl: is_string($thumbnail) ? $thumbnail : null,
                    quality: $quality,
                    filename: $filenamePrefix.'-video-'.($index + 1),
                );
            }
        }

        $candidates = $item['image_versions2']['candidates'] ?? [];
        if (! empty($candidates) && is_array($candidates)) {
            usort($candidates, fn ($a, $b) => ($b['width'] ?? 0) <=> ($a['width'] ?? 0));
            $url = $candidates[0]['url'] ?? null;
            if (is_string($url) && $url !== '') {
                return new MediaItem(
                    url: $url,
                    type: 'image',
                    platform: $this->getPlatformName(),
                    thumbnailUrl: $url,
                    quality: $quality,
                    filename: $filenamePrefix.'-photo-'.($index + 1),
                );
            }
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // GraphQL API
    // -------------------------------------------------------------------------

    private function fetchViaGraphQL(string $shortcode): array
    {
        return $this->requestGraphQL($shortcode, null);
    }

    private function requestGraphQL(string $shortcode, ?string $sessionId): array
    {
        try {
            $csrf = $this->fetchCsrfToken();
            $headers = [
                'Accept' => '*/*',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Origin' => 'https://www.instagram.com',
                'Referer' => "https://www.instagram.com/reel/{$shortcode}/",
                'User-Agent' => self::USER_AGENT_WEB,
                'X-ASBD-ID' => '129477',
                'X-FB-Friendly-Name' => 'PolarisPostRootQuery',
                'X-IG-App-ID' => self::IG_APP_ID,
                'X-Requested-With' => 'XMLHttpRequest',
            ];

            $cookies = [];
            if ($csrf !== null) {
                $headers['X-CSRFToken'] = $csrf;
                $cookies[] = "csrftoken={$csrf}";
            }
            if ($sessionId !== null) {
                $cookies[] = "sessionid={$sessionId}";
            }
            if ($cookies !== []) {
                $headers['Cookie'] = implode('; ', $cookies);
            }

            $response = Http::timeout(15)
                ->withHeaders($headers)
                ->withOptions(['allow_redirects' => ['max' => 3]])
                ->asForm()
                ->post(self::GRAPHQL_URL, [
                    'variables' => json_encode([
                        'shortcode' => $shortcode,
                        '__relay_internal__pv__PolarisAIGMMediaWebLabelEnabledrelayprovider' => false,
                    ], JSON_UNESCAPED_SLASHES),
                    'doc_id' => self::DOC_ID,
                    'server_timestamps' => 'true',
                ]);
        } catch (ConnectionException|Throwable) {
            if ($sessionId !== null) {
                $this->sessionRejected = true;
            }

            return [];
        }

        if ($sessionId !== null && $this->isSessionRejectedResponse($response)) {
            return [];
        }

        if (! $response->ok()) {
            return [];
        }

        $json = $this->decodeInstagramJson($response->body());
        if ($json === null) {
            return [];
        }

        $webInfoItems = $json['data']['xdt_api__v1__media__shortcode__web_info']['items'] ?? null;
        if (is_array($webInfoItems) && $webInfoItems !== []) {
            return $this->parseMobileApiItems($webInfoItems, 'instagram');
        }

        $legacy = $json['data']['xdt_shortcode_media'] ?? null;
        if (is_array($legacy) && $legacy !== []) {
            return $this->parseGraphQLMedia($legacy);
        }

        return [];
    }

    private function fetchCsrfToken(): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => self::USER_AGENT_WEB])
                ->withOptions(['allow_redirects' => ['max' => 2]])
                ->get('https://www.instagram.com/');
        } catch (Throwable) {
            return null;
        }

        foreach ($response->cookies() as $cookie) {
            if ($cookie->getName() === 'csrftoken') {
                return $cookie->getValue();
            }
        }

        if (preg_match('/"csrf_token"\s*:\s*"([^"]+)"/', $response->body(), $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Instagram sometimes prefixes JSON with for (;;);
     *
     * @return array<string, mixed>|null
     */
    private function decodeInstagramJson(string $body): ?array
    {
        $body = trim($body);
        if (str_starts_with($body, 'for (;;);')) {
            $body = substr($body, 9);
        }

        if ($body === '' || str_starts_with($body, '<')) {
            return null;
        }

        $json = json_decode($body, true);

        return is_array($json) ? $json : null;
    }

    private function parseGraphQLMedia(array $media): array
    {
        $typename = $media['__typename'] ?? '';
        $thumbnailUrl = $media['display_url'] ?? null;
        $items = [];

        if ($typename === 'GraphSidecar') {
            $edges = $media['edge_sidecar_to_children']['edges'] ?? [];
            $total = count($edges);
            foreach ($edges as $i => $edge) {
                $node = $edge['node'] ?? [];
                if (! empty($node['is_video'])) {
                    $url = $node['video_url'] ?? null;
                    if ($url) {
                        $items[] = new MediaItem(
                            url: $url,
                            type: 'video',
                            platform: $this->getPlatformName(),
                            thumbnailUrl: $node['display_url'] ?? $thumbnailUrl,
                            quality: $total > 1 ? __('instagram_slide', ['n' => $i + 1, 'total' => $total]) : null,
                            filename: 'instagram-video-'.($i + 1),
                        );
                    }
                } else {
                    $url = $node['display_url'] ?? null;
                    if ($url) {
                        $items[] = new MediaItem(
                            url: $url,
                            type: 'image',
                            platform: $this->getPlatformName(),
                            thumbnailUrl: $url,
                            quality: $total > 1 ? __('instagram_slide', ['n' => $i + 1, 'total' => $total]) : null,
                            filename: 'instagram-photo-'.($i + 1),
                        );
                    }
                }
            }

            return $items;
        }

        if (! empty($media['is_video'])) {
            $videoUrl = $media['video_url'] ?? null;
            if ($videoUrl) {
                $items[] = new MediaItem(
                    url: $videoUrl,
                    type: 'video',
                    platform: $this->getPlatformName(),
                    thumbnailUrl: $thumbnailUrl,
                    filename: 'instagram-video',
                );
            }

            return $items;
        }

        if ($thumbnailUrl) {
            $items[] = new MediaItem(
                url: $thumbnailUrl,
                type: 'image',
                platform: $this->getPlatformName(),
                thumbnailUrl: $thumbnailUrl,
                filename: 'instagram-photo',
            );
        }

        return $items;
    }

    // -------------------------------------------------------------------------
    // yt-dlp fallback
    // -------------------------------------------------------------------------

    private function extractViaYtDlp(string $url, ?string $sessionId): array
    {
        $ytDlp = $this->findYtDlp();
        if ($ytDlp === null) {
            return [];
        }

        $cmd = [$ytDlp, '-J', '--no-warnings', '--no-playlist'];

        if ($sessionId !== null) {
            $cmd[] = '--add-header';
            $cmd[] = "Cookie:sessionid={$sessionId}";
        }

        $cmd[] = $url;

        try {
            $result = Process::timeout(45)->run($cmd);
        } catch (Throwable) {
            return [];
        }

        if (! $result->successful()) {
            $error = $result->errorOutput();
            if ($sessionId !== null && (
                str_contains($error, 'redirect loop')
                || str_contains($error, 'login')
                || str_contains($error, 'cookies')
            )) {
                $this->sessionRejected = true;
            }

            return [];
        }

        $json = json_decode($result->output(), true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($json)) {
            return [];
        }

        return $this->parseYtDlpOutput($json);
    }

    private function parseYtDlpOutput(array $json): array
    {
        $items = [];
        $thumbnail = $json['thumbnail'] ?? null;

        if (! empty($json['entries'])) {
            foreach ($json['entries'] as $i => $entry) {
                $url = $this->bestVideoUrlFromYtDlp($entry);
                if ($url) {
                    $items[] = new MediaItem(
                        url: $url,
                        type: 'video',
                        platform: $this->getPlatformName(),
                        thumbnailUrl: $entry['thumbnail'] ?? $thumbnail,
                        filename: 'instagram-video-'.($i + 1),
                    );
                }
            }

            return $items;
        }

        $mediaType = $json['ext'] ?? 'mp4';
        $isImage = in_array($mediaType, ['jpg', 'jpeg', 'png', 'webp'], true);
        $url = $this->bestVideoUrlFromYtDlp($json);

        if ($url) {
            $items[] = new MediaItem(
                url: $url,
                type: $isImage ? 'image' : 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $thumbnail,
                filename: $isImage ? 'instagram-photo' : 'instagram-video',
            );
        }

        return $items;
    }

    private function bestVideoUrlFromYtDlp(array $entry): ?string
    {
        if (! empty($entry['url'])) {
            return $entry['url'];
        }

        if (! empty($entry['formats'])) {
            $formats = $entry['formats'];
            usort($formats, function ($a, $b) {
                $aScore = ($a['height'] ?? 0) + (isset($a['acodec']) && $a['acodec'] !== 'none' ? 1000 : 0);
                $bScore = ($b['height'] ?? 0) + (isset($b['acodec']) && $b['acodec'] !== 'none' ? 1000 : 0);

                return $bScore <=> $aScore;
            });

            return $formats[0]['url'] ?? null;
        }

        return null;
    }

    private function findYtDlp(): ?string
    {
        foreach (PHP_OS_FAMILY === 'Windows' ? ['yt-dlp.exe', 'yt-dlp'] : ['yt-dlp'] as $cmd) {
            if (Process::run([$cmd, '--version'])->successful()) {
                return $cmd;
            }
        }

        return null;
    }
}
