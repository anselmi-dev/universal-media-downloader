<?php

namespace App\Services\MediaExtractor\Contracts;

use App\DTOs\MediaItem;

interface ExtractorInterface
{
    /**
     * Extract all media items from the given post URL.
     *
     * @return MediaItem[]
     */
    public function extract(string $url): array;

    /**
     * Return a human-readable platform name.
     */
    public function getPlatformName(): string;

    /**
     * Return true if this extractor can handle the given URL.
     */
    public static function supports(string $url): bool;
}
