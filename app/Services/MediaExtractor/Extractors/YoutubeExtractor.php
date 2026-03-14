<?php

namespace App\Services\MediaExtractor\Extractors;

use App\Services\MediaExtractor\Contracts\ExtractorInterface;
use RuntimeException;

class YoutubeExtractor implements ExtractorInterface
{
    public function extract(string $url): array
    {
        throw new RuntimeException(__('errors.platform_coming_soon', ['platform' => 'YouTube Shorts']));
    }

    public function getPlatformName(): string
    {
        return 'YouTube Shorts';
    }

    public static function supports(string $url): bool
    {
        return (bool) preg_match('#(youtube\.com/shorts/|youtu\.be/)#i', $url);
    }
}
