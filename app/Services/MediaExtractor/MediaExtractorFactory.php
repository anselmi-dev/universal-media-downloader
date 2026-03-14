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
     * Ordered list of extractor classes.
     * Add new extractors here to enable support for additional platforms.
     */
    private static array $extractors = [
        TwitterExtractor::class,
        InstagramExtractor::class,
        TiktokExtractor::class,
        RedditExtractor::class,
        YoutubeExtractor::class,
    ];

    /**
     * Resolve the correct extractor for the given URL.
     *
     * @throws InvalidArgumentException when no extractor matches the URL.
     */
    public static function make(string $url): ExtractorInterface
    {
        foreach (self::$extractors as $extractorClass) {
            if ($extractorClass::supports($url)) {
                return new $extractorClass;
            }
        }

        throw new InvalidArgumentException(__('errors.unsupported_platform'));
    }
}
