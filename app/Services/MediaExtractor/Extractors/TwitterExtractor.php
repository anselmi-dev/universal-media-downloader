<?php

namespace App\Services\MediaExtractor\Extractors;

use App\DTOs\MediaItem;
use App\Services\MediaExtractor\Contracts\ExtractorInterface;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class TwitterExtractor implements ExtractorInterface
{
    /**
     * Twitter's public syndication CDN — no API key required.
     * Used for tweet embeds; returns full media URLs.
     */
    private const SYNDICATION_URL = 'https://cdn.syndication.twimg.com/tweet-result';

    public function extract(string $url): array
    {
        $tweetId = $this->extractTweetId($url);
        $data = $this->fetchTweetData($tweetId);

        return $this->parseMediaItems($data);
    }

    public function getPlatformName(): string
    {
        return 'X / Twitter';
    }

    public static function supports(string $url): bool
    {
        return (bool) preg_match('#(twitter\.com|x\.com)/\w+/status/\d+#i', $url);
    }

    private function extractTweetId(string $url): string
    {
        if (! preg_match('#/status/(\d+)#', $url, $matches)) {
            throw new InvalidArgumentException(__('errors.twitter_invalid_url'));
        }

        return $matches[1];
    }

    private function fetchTweetData(string $tweetId): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; MediaDownloader/1.0)',
        ])->get(self::SYNDICATION_URL, [
            'id' => $tweetId,
            'token' => '1',
            'lang' => 'en',
        ]);

        if (! $response->successful()) {
            throw new RuntimeException(__('errors.twitter_fetch_failed'));
        }

        $data = $response->json();

        // Deleted or suspended
        if (($data['__typename'] ?? '') === 'TweetTombstone') {
            throw new RuntimeException(__('errors.post_deleted'));
        }

        if (empty($data) || ($data['__typename'] ?? '') !== 'Tweet') {
            throw new RuntimeException(__('errors.tweet_not_found'));
        }

        return $data;
    }

    private function parseMediaItems(array $data): array
    {
        $mediaItems = [];

        // mediaDetails: array of { type, media_url_https, video_info?, ... }
        $mediaDetails = $data['mediaDetails'] ?? [];

        foreach ($mediaDetails as $item) {
            $type = $item['type'] ?? 'photo';

            if ($type === 'photo') {
                $baseUrl = $item['media_url_https'] ?? null;

                if ($baseUrl) {
                    $baseUrl = preg_replace('/\?.*$/', '', $baseUrl);
                    $originalUrl = $baseUrl.'?format=jpg&name=orig';
                    $thumbUrl = $baseUrl.'?format=jpg&name=large';

                    $mediaItems[] = new MediaItem(
                        url: $originalUrl,
                        type: 'image',
                        platform: $this->getPlatformName(),
                        thumbnailUrl: $thumbUrl,
                    );
                }
            } elseif (in_array($type, ['video', 'animated_gif'])) {
                $variants = $item['video_info']['variants'] ?? [];
                $best = $this->getBestVideoVariant($variants);

                if ($best) {
                    $mediaItems[] = new MediaItem(
                        url: $best['url'],
                        type: 'video',
                        platform: $this->getPlatformName(),
                        thumbnailUrl: $item['media_url_https'] ?? null,
                        quality: isset($best['bit_rate'])
                            ? round($best['bit_rate'] / 1000).' kbps'
                            : null,
                    );
                }
            }
        }

        // Fallback: video object at root (alternative structure)
        if (empty($mediaItems) && isset($data['video']['variants'])) {
            $variants = $data['video']['variants'];
            $mp4 = array_filter($variants, fn ($v) => ($v['type'] ?? '') === 'video/mp4');

            if (! empty($mp4)) {
                usort($mp4, fn ($a, $b) => ($b['bitrate'] ?? 0) <=> ($a['bitrate'] ?? 0));
                $best = reset($mp4);

                $mediaItems[] = new MediaItem(
                    url: $best['src'],
                    type: 'video',
                    platform: $this->getPlatformName(),
                    thumbnailUrl: $data['video']['poster'] ?? null,
                );
            }
        }

        // Fallback: photos array (legacy structure)
        if (empty($mediaItems) && ! empty($data['photos'])) {
            foreach ($data['photos'] as $photo) {
                $url = $photo['url'] ?? $photo['media_url_https'] ?? null;

                if ($url) {
                    $baseUrl = preg_replace('/\?.*$/', '', $url);
                    $mediaItems[] = new MediaItem(
                        url: $baseUrl.'?format=jpg&name=orig',
                        type: 'image',
                        platform: $this->getPlatformName(),
                        thumbnailUrl: $baseUrl.'?format=jpg&name=large',
                    );
                }
            }
        }

        return $mediaItems;
    }

    private function getBestVideoVariant(array $variants): ?array
    {
        $mp4 = array_filter(
            $variants,
            fn ($v) => str_contains($v['content_type'] ?? '', 'mp4')
        );

        if (empty($mp4)) {
            return ! empty($variants) ? reset($variants) : null;
        }

        usort($mp4, fn ($a, $b) => ($b['bit_rate'] ?? 0) <=> ($a['bit_rate'] ?? 0));

        return reset($mp4);
    }
}
