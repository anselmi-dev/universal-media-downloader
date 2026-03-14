<?php

namespace App\DTOs;

class MediaItem
{
    public function __construct(
        public readonly string $url,
        public readonly string $type,
        public readonly string $platform,
        public readonly ?string $thumbnailUrl = null,
        public readonly ?string $quality = null,
        public readonly ?string $filename = null,
    ) {}

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'type' => $this->type,
            'platform' => $this->platform,
            'thumbnailUrl' => $this->thumbnailUrl,
            'quality' => $this->quality,
            'filename' => $this->filename,
        ];
    }
}
