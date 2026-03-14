<?php

namespace App\Services\MediaExtractor;

use App\Services\MediaExtractor\Contracts\ExtractorInterface;
use App\Services\MediaExtractor\Extractors\InstagramExtractor;
use App\Services\MediaExtractor\Extractors\RedditExtractor;
use App\Services\MediaExtractor\Extractors\TiktokExtractor;
use App\Services\MediaExtractor\Extractors\TwitterExtractor;
use App\Services\MediaExtractor\Extractors\YoutubeExtractor;
use InvalidArgumentException;

class MediaExtractorFactory
{
    /**
     * All available extractors, keyed by their platform name.
     * The platform name must match the values used in config/sites.php.
     */
    private static array $extractors = [
        'Twitter'   => TwitterExtractor::class,
        'Instagram' => InstagramExtractor::class,
        'TikTok'    => TiktokExtractor::class,
        'Reddit'    => RedditExtractor::class,
        'YouTube'   => YoutubeExtractor::class,
    ];

    /**
     * Resolve the correct extractor for the given URL.
     *
     * When the current site defines a 'platforms' list (config/sites.php),
     * only extractors in that list are considered — everything else is treated
     * as an unsupported platform for this domain.
     *
     * @throws InvalidArgumentException when no extractor matches the URL.
     */
    public static function make(string $url): ExtractorInterface
    {
        // null = all platforms enabled (default / multi-platform site)
        $allowed = config('site.platforms');

        foreach (self::$extractors as $platform => $extractorClass) {
            if ($allowed !== null && ! in_array($platform, $allowed, true)) {
                continue;
            }

            if ($extractorClass::supports($url)) {
                return new $extractorClass;
            }
        }

        throw new InvalidArgumentException(__('errors.unsupported_platform'));
    }

    /**
     * Return the platform names enabled for the current site.
     * Useful in views to render only the relevant platform chips.
     */
    public static function enabledPlatforms(): array
    {
        return array_keys(
            array_filter(
                self::$extractors,
                fn ($_, $platform) => config('site.platforms') === null
                    || in_array($platform, config('site.platforms'), true),
                ARRAY_FILTER_USE_BOTH,
            )
        );
    }
}
