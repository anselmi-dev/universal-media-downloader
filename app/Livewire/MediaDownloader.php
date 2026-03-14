<?php

namespace App\Livewire;

use App\Services\MediaExtractor\MediaExtractorFactory;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class MediaDownloader extends Component
{
    #[Validate('required|url|max:500')]
    public string $url = '';

    public array $mediaItems = [];

    public ?string $error = null;

    public ?string $platform = null;

    public bool $hasSearched = false;

    public function download(): void
    {
        $this->validate();

        $this->error = null;
        $this->mediaItems = [];
        $this->platform = null;
        $this->hasSearched = true;

        try {
            $extractor = MediaExtractorFactory::make($this->url);
            $this->platform = $extractor->getPlatformName();

            $items = $extractor->extract($this->url);

            if (empty($items)) {
                $this->error = __('No media found in this post. The post may not contain any images or videos.');

                return;
            }

            // Convert DTOs to plain arrays for Livewire serialization
            $this->mediaItems = array_map(
                fn ($item) => $item->toArray(),
                $items
            );
        } catch (Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.media-downloader');
    }
}
