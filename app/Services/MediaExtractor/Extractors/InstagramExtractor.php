<?php

namespace App\Services\MediaExtractor\Extractors;

use App\DTOs\MediaItem;
use App\Services\MediaExtractor\Contracts\ExtractorInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use InvalidArgumentException;
use RuntimeException;

class InstagramExtractor implements ExtractorInterface
{
    private const USER_AGENT_WEB = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * Mobile app User-Agent accepted by Instagram's private API.
     * Using the Android Instagram app signature.
     */
    private const USER_AGENT_APP = 'Instagram 195.0.0.31.123 Android (29/10; 480dpi; 1080x2032; HUAWEI; ANE-LX1; HWANE; en_US; 302733750)';

    private const IG_APP_ID = '936619743392459';

    private const GRAPHQL_HEADERS = [
        'Accept'             => '*/*',
        'Accept-Language'    => 'en-US,en;q=0.5',
        'Content-Type'       => 'application/x-www-form-urlencoded',
        'X-FB-Friendly-Name' => 'PolarisPostActionLoadPostQueryQuery',
        'X-CSRFToken'        => 'RVDUooU5MYsBbS1CNN3CzVAuEP8oHB52',
        'X-IG-App-ID'        => '1217981644879628',
        'X-FB-LSD'           => 'AVqbxe3J_YA',
        'X-ASBD-ID'          => '129477',
        'Sec-Fetch-Dest'     => 'empty',
        'Sec-Fetch-Mode'     => 'cors',
        'Sec-Fetch-Site'     => 'same-origin',
        'User-Agent'         => 'Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-G973U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/14.2 Chrome/87.0.4280.141 Mobile Safari/537.36',
    ];

    /** doc_id for PolarisPostActionLoadPostQueryQuery – update when Instagram rotates it */
    private const DOC_ID = '10015901848480474';

    // -------------------------------------------------------------------------
    // Public interface
    // -------------------------------------------------------------------------

    public function extract(string $url): array
    {
        $shortcode = $this->extractShortcode($url);
        $isStory   = $this->isStoryUrl($url);
        $sessionId = $this->getSessionId();

        // 1. For stories/highlights: try Instagram's authenticated mobile API
        //    (requires INSTAGRAM_SESSION_ID in .env)
        if ($isStory && $sessionId !== null) {
            $items = $this->isHighlightUrl($url)
                ? $this->fetchHighlightItems($shortcode, $sessionId)
                : $this->fetchStoryItems($url, $sessionId);

            if (! empty($items)) {
                return $items;
            }
        }

        // 2. For posts/reels: try Instagram's internal GraphQL API (no auth needed)
        if (! $isStory) {
            // 2a. With session for best results
            if ($sessionId !== null) {
                $items = $this->fetchPostViaAuthApi($shortcode, $sessionId);
                if (! empty($items)) {
                    return $items;
                }
            }

            // 2b. Anonymous GraphQL fallback
            $items = $this->fetchViaGraphQL($shortcode);
            if (! empty($items)) {
                return $items;
            }
        }

        // 3. yt-dlp universal fallback (handles all types, passes session if set)
        $items = $this->extractViaYtDlp($url, $sessionId);
        if (! empty($items)) {
            return $items;
        }

        // 4. Friendly error
        if ($isStory) {
            if ($sessionId === null) {
                throw new RuntimeException(__('errors.instagram_story_no_session'));
            }
            throw new RuntimeException(__('errors.instagram_story_requires_auth'));
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
     *
     * /p/{shortcode}/
     * /reel/{shortcode}/
     * /tv/{shortcode}/
     * /stories/{username}/{media_id}/
     * /stories/highlights/{highlight_id}/
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

        // URL-decode in case the value was copied from the browser address bar
        return urldecode($id);
    }

    // -------------------------------------------------------------------------
    // Authenticated Instagram API (requires INSTAGRAM_SESSION_ID)
    // -------------------------------------------------------------------------

    /**
     * Fetch highlight items via Instagram's mobile API.
     * Endpoint: GET i.instagram.com/api/v1/feed/reels_media/?reel_ids=highlight:{id}
     */
    private function fetchHighlightItems(string $highlightId, string $sessionId): array
    {
        $response = Http::timeout(15)
            ->withHeaders($this->authHeaders($sessionId))
            ->get('https://i.instagram.com/api/v1/feed/reels_media/', [
                'reel_ids' => "highlight:{$highlightId}",
            ]);

        if (! $response->ok()) {
            return [];
        }

        return $this->parseReelsMediaResponse($response->json());
    }

    /**
     * Fetch a specific story item using its media ID directly.
     * Endpoint: GET i.instagram.com/api/v1/media/{mediaId}/info/
     *
     * The media ID is already in the story URL (no need to resolve userId first).
     */
    private function fetchStoryItems(string $storyUrl, string $sessionId): array
    {
        // Extract the numeric media ID from the URL: /stories/{username}/{mediaId}/
        if (! preg_match('#instagram\.com/stories/[\w.]+/([\d]+)#i', $storyUrl, $m)) {
            return [];
        }

        $mediaId  = $m[1];
        $response = Http::timeout(15)
            ->withHeaders($this->authHeaders($sessionId))
            ->get("https://i.instagram.com/api/v1/media/{$mediaId}/info/");

        if (! $response->ok()) {
            return [];
        }

        $json  = $response->json();
        $items = $json['items'] ?? [];

        return array_values(array_filter(array_map(
            fn ($item, $i) => $this->parseMobileApiItem($item, $i, count($items)),
            $items,
            array_keys($items),
        )));
    }

    /**
     * Parse the response from /api/v1/feed/reels_media/ into MediaItem DTOs.
     * Response shape: { reels: { "highlight:{id}": { items: [...] } } }
     */
    private function parseReelsMediaResponse(array $json): array
    {
        $items = [];

        foreach ($json['reels'] ?? [] as $reel) {
            $reel_items = $reel['items'] ?? [];
            $total      = count($reel_items);
            foreach ($reel_items as $i => $item) {
                $parsed = $this->parseMobileApiItem($item, $i, $total);
                if ($parsed) {
                    $items[] = $parsed;
                }
            }
        }

        return $items;
    }

    /**
     * Fetch a regular post/reel via the authenticated mobile API.
     * Endpoint: GET i.instagram.com/api/v1/media/{shortcode}/info/
     * (shortcode must be converted to numeric media ID first via GraphQL)
     */
    private function fetchPostViaAuthApi(string $shortcode, string $sessionId): array
    {
        // Use the web API with session cookie for better success rate on public posts
        $response = Http::timeout(12)
            ->withHeaders(array_merge(self::GRAPHQL_HEADERS, [
                'Cookie' => "sessionid={$sessionId}",
            ]))
            ->asForm()
            ->post('https://www.instagram.com/api/graphql', $this->graphqlParams($shortcode));

        if (! $response->ok()) {
            return [];
        }

        $media = $response->json('data.xdt_shortcode_media');
        if (empty($media)) {
            return [];
        }

        return $this->parseGraphQLMedia($media);
    }

    /**
     * Build authenticated headers for Instagram's mobile API.
     */
    private function authHeaders(string $sessionId): array
    {
        return [
            'User-Agent'        => self::USER_AGENT_APP,
            'X-IG-App-ID'       => self::IG_APP_ID,
            'Accept-Language'   => 'en-US',
            'Accept-Encoding'   => 'gzip, deflate',
            'Cookie'            => "sessionid={$sessionId}; ig_did=UNKNOWN",
            'X-IG-Connection-Type' => 'WIFI',
        ];
    }

    /**
     * Parse a single item from Instagram's mobile API response (story/highlight).
     * Returns a MediaItem or null if nothing extractable.
     */
    private function parseMobileApiItem(array $item, int $index, int $total): ?MediaItem
    {
        $thumbnail = $item['image_versions2']['candidates'][0]['url'] ?? null;
        $quality   = $total > 1 ? __('instagram_slide', ['n' => $index + 1, 'total' => $total]) : null;

        // Video story / highlight
        if (! empty($item['video_versions'])) {
            // Sort by bitrate/width descending
            $versions = $item['video_versions'];
            usort($versions, fn ($a, $b) => ($b['width'] ?? 0) <=> ($a['width'] ?? 0));
            $url = $versions[0]['url'] ?? null;
            if ($url) {
                return new MediaItem(
                    url: $url,
                    type: 'video',
                    platform: $this->getPlatformName(),
                    thumbnailUrl: $thumbnail,
                    quality: $quality,
                    filename: 'instagram-story-'.($index + 1),
                );
            }
        }

        // Image story / highlight
        $candidates = $item['image_versions2']['candidates'] ?? [];
        if (! empty($candidates)) {
            usort($candidates, fn ($a, $b) => ($b['width'] ?? 0) <=> ($a['width'] ?? 0));
            $url = $candidates[0]['url'] ?? null;
            if ($url) {
                return new MediaItem(
                    url: $url,
                    type: 'image',
                    platform: $this->getPlatformName(),
                    thumbnailUrl: $thumbnail,
                    quality: $quality,
                    filename: 'instagram-story-'.($index + 1),
                );
            }
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Anonymous GraphQL API (public posts, no auth)
    // -------------------------------------------------------------------------

    private function fetchViaGraphQL(string $shortcode): array
    {
        $response = Http::timeout(12)
            ->withHeaders(self::GRAPHQL_HEADERS)
            ->asForm()
            ->post('https://www.instagram.com/api/graphql', $this->graphqlParams($shortcode));

        if (! $response->ok()) {
            return [];
        }

        $media = $response->json('data.xdt_shortcode_media');
        if (empty($media)) {
            return [];
        }

        return $this->parseGraphQLMedia($media);
    }

    private function graphqlParams(string $shortcode): array
    {
        return [
            'av'                          => '0',
            '__d'                         => 'www',
            '__user'                      => '0',
            '__a'                         => '1',
            '__req'                       => '3',
            '__hs'                        => '19624.HYP:instagram_web_pkg.2.1..0.0',
            'dpr'                         => '3',
            '__ccg'                       => 'UNKNOWN',
            '__rev'                       => '1008824440',
            '__s'                         => 'xf44ne:zhh75g:xr51e7',
            '__hsi'                       => '7282217488877343271',
            '__dyn'                       => '7xeUmwlEnwn8K2WnFw9-2i5U4e0yoW3q32360CEbo1nEhw2nVE4W0om78b87C0yE5ufz81s8hwGwQwoEcE7O2l0Fwqo31w9a9x-0z8-U2zxe2GewGwso88cobEaU2eUlwhEe87q7-0iK2S3qazo7u1xwIw8O321LwTwKG1pg661pwr86C1mwraCg',
            '__csr'                       => '',
            '__comet_req'                 => '7',
            'lsd'                         => 'AVqbxe3J_YA',
            'jazoest'                     => '2957',
            '__spin_r'                    => '1008824440',
            '__spin_b'                    => 'trunk',
            '__spin_t'                    => '1695523385',
            'fb_api_caller_class'         => 'RelayModern',
            'fb_api_req_friendly_name'    => 'PolarisPostActionLoadPostQueryQuery',
            'variables'                   => json_encode([
                'shortcode'                         => $shortcode,
                'fetch_comment_count'               => null,
                'fetch_related_profile_media_count' => null,
                'parent_comment_count'              => null,
                'child_comment_count'               => null,
                'fetch_like_count'                  => null,
                'fetch_tagged_user_count'           => null,
                'fetch_preview_comment_count'       => null,
                'has_threaded_comments'             => false,
                'hoisted_comment_id'                => null,
                'hoisted_reply_id'                  => null,
            ]),
            'server_timestamps'           => 'true',
            'doc_id'                      => self::DOC_ID,
        ];
    }

    private function parseGraphQLMedia(array $media): array
    {
        $typename     = $media['__typename'] ?? '';
        $thumbnailUrl = $media['display_url'] ?? null;
        $items        = [];

        // Carousel (sidecar)
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

        // Video / Reel
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

        // Single image
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

        $result = Process::timeout(30)->run($cmd);
        if (! $result->successful()) {
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
        $items     = [];
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
        $isImage   = in_array($mediaType, ['jpg', 'jpeg', 'png', 'webp'], true);
        $url       = $this->bestVideoUrlFromYtDlp($json);

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
