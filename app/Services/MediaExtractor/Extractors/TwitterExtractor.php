<?php

namespace App\Services\MediaExtractor\Extractors;

use App\DTOs\MediaItem;
use App\Services\MediaExtractor\Contracts\ExtractorInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use InvalidArgumentException;
use RuntimeException;

class TwitterExtractor implements ExtractorInterface
{
    /**
     * Twitter's public syndication CDN — no API key required.
     * Used as fallback when yt-dlp is unavailable; often returns lower video quality.
     */
    private const SYNDICATION_URL = 'https://cdn.syndication.twimg.com/tweet-result';

    public function extract(string $url): array
    {
        // 1. yt-dlp first — GraphQL/legacy APIs expose higher-quality progressive MP4s
        $items = $this->extractViaYtDlp($url);
        if (! empty($items)) {
            return $items;
        }

        // 2. Fallback: syndication embed API (no install needed)
        $tweetId = $this->extractTweetId($url);
        $data = $this->fetchTweetData($tweetId);

        return $this->parseSyndicationMedia($data);
    }

    public function getPlatformName(): string
    {
        return 'X / Twitter';
    }

    public static function supports(string $url): bool
    {
        return (bool) preg_match('#(twitter\.com|x\.com)/\w+/status/\d+#i', $url);
    }

    // -------------------------------------------------------------------------
    // yt-dlp (primary)
    // -------------------------------------------------------------------------

    private function extractViaYtDlp(string $url): array
    {
        $ytDlp = $this->findYtDlp();
        if ($ytDlp === null) {
            return [];
        }

        $result = Process::timeout(30)->run(array_merge($ytDlp, [
            '-J',
            '--no-warnings',
            '--no-playlist',
            // Prefer GraphQL (best quality), then legacy, then syndication
            '--extractor-args',
            'twitter:api=graphql,legacy,syndication',
            $url,
        ]));

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
        $items = [];

        if (! empty($json['entries']) && is_array($json['entries'])) {
            foreach ($json['entries'] as $i => $entry) {
                if (! is_array($entry)) {
                    continue;
                }
                $item = $this->mediaItemFromYtDlpEntry($entry, $i + 1);
                if ($item !== null) {
                    $items[] = $item;
                }
            }

            return $items;
        }

        $item = $this->mediaItemFromYtDlpEntry($json, 1);

        return $item !== null ? [$item] : [];
    }

    private function mediaItemFromYtDlpEntry(array $entry, int $index): ?MediaItem
    {
        $ext = strtolower((string) ($entry['ext'] ?? 'mp4'));
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);

        if ($isImage) {
            $url = $entry['url'] ?? $this->bestImageUrlFromYtDlp($entry);
            if (empty($url)) {
                return null;
            }

            return new MediaItem(
                url: $url,
                type: 'image',
                platform: $this->getPlatformName(),
                thumbnailUrl: $entry['thumbnail'] ?? $url,
                filename: 'twitter-photo-'.$index,
            );
        }

        $best = $this->pickBestProgressiveFormat($entry['formats'] ?? []);
        $url = $best['url'] ?? $entry['url'] ?? null;

        // Reject HLS/m3u8 — the download proxy streams progressive files only
        if (empty($url) || $this->isHlsUrl($url)) {
            return null;
        }

        $height = (int) ($best['height'] ?? $entry['height'] ?? 0);
        $bitrate = (int) ($best['tbr'] ?? $best['vbr'] ?? 0);

        return new MediaItem(
            url: $url,
            type: 'video',
            platform: $this->getPlatformName(),
            thumbnailUrl: $entry['thumbnail'] ?? null,
            quality: $this->formatQualityLabel($height, $bitrate),
            filename: 'twitter-video-'.$index,
        );
    }

    /**
     * Pick the best progressive (non-HLS) MP4 with video+audio for direct proxy download.
     */
    private function pickBestProgressiveFormat(array $formats): ?array
    {
        if (empty($formats)) {
            return null;
        }

        $progressive = array_values(array_filter($formats, function ($f) {
            if (! is_array($f) || empty($f['url'])) {
                return false;
            }

            $vcodec = $f['vcodec'] ?? 'none';
            $protocol = (string) ($f['protocol'] ?? '');
            $ext = strtolower((string) ($f['ext'] ?? ''));

            if ($vcodec === 'none') {
                return false;
            }

            if (str_contains($protocol, 'm3u8') || $this->isHlsUrl($f['url'])) {
                return false;
            }

            // Prefer mp4; allow unknown ext if URL looks like video.twimg progressive
            if ($ext !== '' && $ext !== 'mp4') {
                return false;
            }

            return true;
        }));

        if (empty($progressive)) {
            return null;
        }

        usort($progressive, function ($a, $b) {
            $score = function (array $f): int {
                $height = (int) ($f['height'] ?? 0);
                $hasAudio = isset($f['acodec']) && $f['acodec'] !== 'none' ? 1_000_000 : 0;
                $br = (int) ($f['tbr'] ?? $f['vbr'] ?? $f['abr'] ?? 0);

                return $hasAudio + ($height * 1000) + $br;
            };

            return $score($b) <=> $score($a);
        });

        return $progressive[0];
    }

    private function bestImageUrlFromYtDlp(array $entry): ?string
    {
        if (! empty($entry['url']) && ! $this->isHlsUrl($entry['url'])) {
            return $entry['url'];
        }

        $formats = $entry['formats'] ?? [];
        usort($formats, fn ($a, $b) => ((int) ($b['width'] ?? 0)) <=> ((int) ($a['width'] ?? 0)));

        foreach ($formats as $format) {
            $url = $format['url'] ?? null;
            if (! empty($url) && ! $this->isHlsUrl($url)) {
                return $url;
            }
        }

        return $entry['thumbnail'] ?? null;
    }

    private function formatQualityLabel(int $height, int $bitrate): ?string
    {
        if ($height > 0) {
            return $height.'p';
        }

        if ($bitrate > 0) {
            return round($bitrate).' kbps';
        }

        return null;
    }

    private function isHlsUrl(string $url): bool
    {
        return str_contains($url, '.m3u8') || str_contains($url, '/pl/');
    }

    /**
     * @return list<string>|null Command prefix, e.g. ['yt-dlp'] or ['python', '-m', 'yt_dlp']
     */
    private function findYtDlp(): ?array
    {
        $candidates = PHP_OS_FAMILY === 'Windows'
            ? [['yt-dlp.exe'], ['yt-dlp'], ['python', '-m', 'yt_dlp']]
            : [['yt-dlp'], ['python3', '-m', 'yt_dlp'], ['python', '-m', 'yt_dlp']];

        foreach ($candidates as $cmd) {
            if (Process::run(array_merge($cmd, ['--version']))->successful()) {
                return $cmd;
            }
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Syndication fallback
    // -------------------------------------------------------------------------

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

        if (($data['__typename'] ?? '') === 'TweetTombstone') {
            throw new RuntimeException(__('errors.post_deleted'));
        }

        if (empty($data) || ($data['__typename'] ?? '') !== 'Tweet') {
            throw new RuntimeException(__('errors.tweet_not_found'));
        }

        return $data;
    }

    private function parseSyndicationMedia(array $data): array
    {
        $mediaItems = [];
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
            } elseif (in_array($type, ['video', 'animated_gif'], true)) {
                $variants = $item['video_info']['variants'] ?? [];
                $best = $this->getBestVideoVariant($variants);

                if ($best) {
                    $bitrate = (int) ($best['bitrate'] ?? $best['bit_rate'] ?? 0);

                    $mediaItems[] = new MediaItem(
                        url: $best['url'],
                        type: 'video',
                        platform: $this->getPlatformName(),
                        thumbnailUrl: $item['media_url_https'] ?? null,
                        quality: $bitrate > 0 ? round($bitrate / 1000).' kbps' : null,
                    );
                }
            }
        }

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
        $mp4 = array_values(array_filter(
            $variants,
            fn ($v) => str_contains($v['content_type'] ?? '', 'mp4')
        ));

        if (empty($mp4)) {
            return ! empty($variants) ? reset($variants) : null;
        }

        // Syndication uses "bitrate"; some payloads use "bit_rate"
        usort($mp4, function ($a, $b) {
            $brA = (int) ($a['bitrate'] ?? $a['bit_rate'] ?? 0);
            $brB = (int) ($b['bitrate'] ?? $b['bit_rate'] ?? 0);

            return $brB <=> $brA;
        });

        return $mp4[0];
    }
}
