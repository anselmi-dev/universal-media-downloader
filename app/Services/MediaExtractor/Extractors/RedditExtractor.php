<?php

namespace App\Services\MediaExtractor\Extractors;

use App\DTOs\MediaItem;
use App\Services\MediaExtractor\Contracts\ExtractorInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RedditExtractor implements ExtractorInterface
{
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    // -------------------------------------------------------------------------
    // Public interface
    // -------------------------------------------------------------------------

    public function extract(string $url): array
    {
        $jsonUrl = $this->toJsonUrl($url);
        $post    = $this->fetchPost($jsonUrl);

        // Follow crossposts to the original post data
        $source = $post['crosspost_parent_list'][0] ?? $post;

        // ── Reddit-hosted video ───────────────────────────────────────────────
        if (! empty($source['is_video']) && ! empty($source['media']['reddit_video'])) {
            $items = $this->extractVideo($source['media']['reddit_video'], $source);
            if (! empty($items)) {
                return $items;
            }
        }

        // ── Hosted video via preview (e.g. gifv-converted GIF) ───────────────
        if (! empty($source['preview']['reddit_video_preview'])) {
            $items = $this->extractVideo($source['preview']['reddit_video_preview'], $source);
            if (! empty($items)) {
                return $items;
            }
        }

        // ── Gallery (multiple images) ─────────────────────────────────────────
        if (! empty($source['is_gallery']) && ! empty($source['media_metadata'])) {
            $items = $this->extractGallery($source);
            if (! empty($items)) {
                return $items;
            }
        }

        // ── Single image / GIF ────────────────────────────────────────────────
        $directUrl = $source['url'] ?? null;
        if ($directUrl && $this->isDirectMedia($directUrl)) {
            return $this->wrapDirectUrl($directUrl, $source);
        }

        // ── Preview image fallback ────────────────────────────────────────────
        if (! empty($source['preview']['images'])) {
            $items = $this->extractPreviewImages($source['preview']['images']);
            if (! empty($items)) {
                return $items;
            }
        }

        throw new RuntimeException(__('errors.reddit_no_media'));
    }

    public function getPlatformName(): string
    {
        return 'Reddit';
    }

    /**
     * Supports:
     *   https://www.reddit.com/r/{sub}/comments/{id}/{title}/
     *   https://old.reddit.com/r/{sub}/comments/{id}/
     *   https://redd.it/{id}
     *   https://reddit.com/gallery/{id}
     */
    public static function supports(string $url): bool
    {
        return (bool) preg_match(
            '#(?:reddit\.com/r/\w+/comments/|reddit\.com/gallery/|redd\.it/)#i',
            $url
        );
    }

    // -------------------------------------------------------------------------
    // URL helpers
    // -------------------------------------------------------------------------

    /**
     * Convert any Reddit post URL into the corresponding JSON API URL.
     * Handles short links (redd.it) by following redirects.
     */
    private function toJsonUrl(string $url): string
    {
        // Short URL: resolve first
        if (preg_match('#redd\.it/#i', $url)) {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => self::USER_AGENT])
                ->withOptions(['allow_redirects' => false])
                ->get($url);

            $location = $response->header('Location');
            if ($location) {
                $url = $location;
            }
        }

        // Normalise to www.reddit.com
        $url = preg_replace('#^https?://(?:old\.|new\.)?reddit\.com#i', 'https://www.reddit.com', $url);

        // Strip existing query string / fragment
        $url = strtok($url, '?');
        $url = strtok($url, '#');

        // Append .json
        $url = rtrim($url, '/') . '.json';

        return $url;
    }

    // -------------------------------------------------------------------------
    // API call
    // -------------------------------------------------------------------------

    private function fetchPost(string $jsonUrl): array
    {
        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => self::USER_AGENT])
            ->get($jsonUrl);

        if (! $response->ok()) {
            throw new RuntimeException(__('errors.reddit_fetch_failed'));
        }

        $json = $response->json();
        $post = $json[0]['data']['children'][0]['data'] ?? null;

        if (empty($post)) {
            throw new RuntimeException(__('errors.reddit_fetch_failed'));
        }

        return $post;
    }

    // -------------------------------------------------------------------------
    // Media extractors
    // -------------------------------------------------------------------------

    /**
     * Extract Reddit-hosted video at multiple quality levels + audio track.
     *
     * Reddit stores video (H.264) and audio as separate DASH streams.
     * We expose each quality as a separate item so the user can pick the best
     * one, and add an "Audio only" item for users who want to merge manually.
     */
    private function extractVideo(array $redditVideo, array $post): array
    {
        $fallbackUrl = $redditVideo['fallback_url'] ?? null;
        if (empty($fallbackUrl)) {
            return [];
        }

        // Strip query params: https://v.redd.it/{id}/DASH_720.mp4
        $cleanUrl = (string) strtok($fallbackUrl, '?');

        // Extract video ID from path
        if (! preg_match('#v\.redd\.it/([^/]+)/#', $cleanUrl, $m)) {
            return [];
        }

        $videoId   = $m[1];
        $maxHeight = (int) ($redditVideo['height'] ?? 720);
        $isGif     = ! empty($redditVideo['is_gif']);
        $thumbnail = $this->thumbnailFrom($post);
        $items     = [];

        // Derive quality variants from the known DASH naming pattern
        foreach ([1080, 720, 480, 360, 240] as $height) {
            if ($height > $maxHeight) {
                continue;
            }

            $items[] = new MediaItem(
                url: "https://v.redd.it/{$videoId}/DASH_{$height}.mp4",
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $thumbnail,
                quality: "{$height}p" . ($isGif ? '' : ' ⚠ no audio'),
                filename: "reddit-video-{$height}p",
            );
        }

        // Audio track (not present for GIFs)
        if (! $isGif) {
            $items[] = new MediaItem(
                url: "https://v.redd.it/{$videoId}/DASH_audio.mp4",
                type: 'audio',
                platform: $this->getPlatformName(),
                thumbnailUrl: $thumbnail,
                quality: __('reddit_audio'),
                filename: 'reddit-audio',
            );
        }

        return $items;
    }

    /**
     * Extract all images from a Reddit gallery post.
     * Full-resolution images live at i.redd.it/{media_id}.{ext}
     */
    private function extractGallery(array $post): array
    {
        $items    = [];
        $metadata = $post['media_metadata'] ?? [];
        $order    = array_column($post['gallery_data']['items'] ?? [], 'media_id');
        $total    = count($order);

        // Use the ordered list from gallery_data so items appear in the right sequence
        foreach ($order as $index => $mediaId) {
            $meta = $metadata[$mediaId] ?? [];
            $mime = $meta['m'] ?? 'image/jpeg';
            $ext  = $this->mimeToExt($mime);

            // Full-resolution URL
            $url = "https://i.redd.it/{$mediaId}.{$ext}";

            // Thumbnail: largest preview available
            $preview = ! empty($meta['p'])
                ? html_entity_decode(end($meta['p'])['u'] ?? '')
                : null;

            $items[] = new MediaItem(
                url: $url,
                type: str_contains($mime, 'gif') ? 'video' : 'image',
                platform: $this->getPlatformName(),
                thumbnailUrl: $preview ?: $url,
                quality: $total > 1 ? __('reddit_slide', ['n' => $index + 1, 'total' => $total]) : null,
                filename: 'reddit-image-' . ($index + 1),
            );
        }

        return $items;
    }

    /**
     * Extract preview images (fallback when no direct media URL is available).
     */
    private function extractPreviewImages(array $images): array
    {
        $items = [];
        $total = count($images);

        foreach ($images as $i => $image) {
            // Use highest-resolution variant
            $resolutions = $image['resolutions'] ?? [];
            $source      = $image['source'] ?? null;
            $best        = $source ?? (! empty($resolutions) ? end($resolutions) : null);

            if (empty($best['url'])) {
                continue;
            }

            $url = html_entity_decode($best['url']);

            $items[] = new MediaItem(
                url: $url,
                type: 'image',
                platform: $this->getPlatformName(),
                thumbnailUrl: $url,
                quality: $total > 1 ? __('reddit_slide', ['n' => $i + 1, 'total' => $total]) : null,
                filename: 'reddit-image-' . ($i + 1),
            );
        }

        return $items;
    }

    /**
     * Wrap a direct media URL (image, GIF, Imgur .gifv) into a MediaItem.
     */
    private function wrapDirectUrl(string $url, array $post): array
    {
        // Convert Imgur .gifv to .mp4
        if (str_ends_with($url, '.gifv')) {
            $url = substr($url, 0, -5) . '.mp4';

            return [new MediaItem(
                url: $url,
                type: 'video',
                platform: $this->getPlatformName(),
                thumbnailUrl: $this->thumbnailFrom($post),
                filename: 'reddit-video',
            )];
        }

        $ext  = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $type = in_array($ext, ['mp4', 'webm', 'mov'], true) ? 'video' : 'image';

        return [new MediaItem(
            url: $url,
            type: $type,
            platform: $this->getPlatformName(),
            thumbnailUrl: $this->thumbnailFrom($post),
            filename: 'reddit-media',
        )];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function isDirectMedia(string $url): bool
    {
        return (bool) preg_match(
            '#\.(jpg|jpeg|png|gif|webp|gifv|mp4|webm)(\?|$)#i',
            $url
        );
    }

    private function thumbnailFrom(array $post): ?string
    {
        $thumbnail = $post['thumbnail'] ?? null;

        // Reddit sometimes sets thumbnail to 'self', 'default', 'spoiler', etc.
        if ($thumbnail && filter_var($thumbnail, FILTER_VALIDATE_URL)) {
            return $thumbnail;
        }

        // Try preview image
        $previewUrl = $post['preview']['images'][0]['source']['url'] ?? null;

        return $previewUrl ? html_entity_decode($previewUrl) : null;
    }

    private function mimeToExt(string $mime): string
    {
        return match (true) {
            str_contains($mime, 'png')  => 'png',
            str_contains($mime, 'gif')  => 'gif',
            str_contains($mime, 'webp') => 'webp',
            default                     => 'jpg',
        };
    }
}
