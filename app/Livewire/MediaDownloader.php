<?php

namespace App\Livewire;

use App\Models\DownloadRequest;
use App\Services\MediaExtractor\MediaExtractorFactory;
use App\Services\TelegramNotifier;
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

        $this->error      = null;
        $this->mediaItems = [];
        $this->platform   = null;
        $this->hasSearched = true;

        try {
            $extractor      = MediaExtractorFactory::make($this->url);
            $this->platform = $extractor->getPlatformName();

            $items = $extractor->extract($this->url);

            if (empty($items)) {
                $this->logRequest(DownloadRequest::STATUS_NO_MEDIA, __('No media found in this post. The post may not contain any images or videos.'));
                $this->error = __('No media found in this post. The post may not contain any images or videos.');

                return;
            }

            // Convert DTOs to plain arrays for Livewire serialization
            $this->mediaItems = array_map(fn ($item) => $item->toArray(), $items);

            $this->logRequest(DownloadRequest::STATUS_SUCCESS, null, count($this->mediaItems));

            // Notify the browser so Alpine can persist this entry to the history
            $this->dispatch('download-success',
                url:      $this->url,
                platform: $this->platform,
                label:    $this->labelFromUrl($this->url, $this->platform),
                thumb:    $this->firstThumbnail($this->mediaItems),
                count:    count($this->mediaItems),
            );

            $this->url = '';
        } catch (Throwable $e) {
            $this->logRequest(DownloadRequest::STATUS_ERROR, $e->getMessage());
            $this->error = $e->getMessage();
        }
    }

    private function logRequest(string $status, ?string $errorMessage = null, ?int $itemsCount = null): void
    {
        try {
            $data = [
                'url'           => $this->url,
                'platform'      => $this->platform,
                'status'        => $status,
                'error_message' => $errorMessage,
                'items_count'   => $itemsCount,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'site_host'     => request()->getHost(),
            ];

            DownloadRequest::create($data);

            $notifier = app(TelegramNotifier::class);
            if ($notifier->isConfigured()) {
                $notifier->notifyDownloadRequest($data);
            }
        } catch (Throwable) {
            // Silently ignore logging failures to not break the download flow
        }
    }

    public function render()
    {
        return view('livewire.media-downloader');
    }

    // -------------------------------------------------------------------------
    // History helpers
    // -------------------------------------------------------------------------

    /**
     * Derive a short, human-readable label from the post URL.
     * Uses the slug, username, or shortcode depending on the platform.
     */
    private function labelFromUrl(string $url, string $platform): string
    {
        return match ($platform) {
            'Reddit'    => $this->redditLabel($url),
            'Twitter'   => $this->twitterLabel($url),
            'TikTok'    => $this->tiktokLabel($url),
            'Instagram' => $this->instagramLabel($url),
            default     => $this->genericLabel($url),
        };
    }

    private function redditLabel(string $url): string
    {
        // /r/{sub}/comments/{id}/{title_slug}/
        if (preg_match('#/r/([\w]+)/comments/\w+/([^/]+)#i', $url, $m)) {
            $sub  = $m[1];
            $slug = str_replace('_', ' ', urldecode($m[2]));
            $slug = mb_strtolower(mb_substr($slug, 0, 60));

            return "r/{$sub} · {$slug}";
        }

        // /r/{sub}/
        if (preg_match('#/r/([\w]+)#i', $url, $m)) {
            return "r/{$m[1]}";
        }

        return $this->genericLabel($url);
    }

    private function twitterLabel(string $url): string
    {
        // x.com/{user}/status/{id} or twitter.com/{user}/status/{id}
        if (preg_match('#(?:twitter|x)\.com/([\w]+)/status/(\d+)#i', $url, $m)) {
            return "@{$m[1]} · #{$m[2]}";
        }

        return $this->genericLabel($url);
    }

    private function tiktokLabel(string $url): string
    {
        // tiktok.com/@{user}/video/{id}
        if (preg_match('#tiktok\.com/@([\w.]+)/video/(\d+)#i', $url, $m)) {
            return "@{$m[1]}";
        }

        return $this->genericLabel($url);
    }

    private function instagramLabel(string $url): string
    {
        // /stories/highlights/{id}
        if (preg_match('#/stories/highlights/([\w-]+)#i', $url, $m)) {
            return "Highlight {$m[1]}";
        }

        // /stories/{username}/{id}
        if (preg_match('#/stories/([\w.]+)/#i', $url, $m)) {
            return "@{$m[1]} · Story";
        }

        // /reel/{code}
        if (preg_match('#/reel/([\w-]+)#i', $url, $m)) {
            return "Reel {$m[1]}";
        }

        // /p/{code}
        if (preg_match('#/p/([\w-]+)#i', $url, $m)) {
            return "Post {$m[1]}";
        }

        return $this->genericLabel($url);
    }

    private function genericLabel(string $url): string
    {
        $host = preg_replace('/^www\./', '', parse_url($url, PHP_URL_HOST) ?? '');
        $path = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
        $path = mb_substr($path, 0, 50);

        return $host . ($path ? " · {$path}" : '');
    }

    /** Return the thumbnailUrl of the first item that has one. */
    private function firstThumbnail(array $mediaItems): ?string
    {
        foreach ($mediaItems as $item) {
            if (! empty($item['thumbnailUrl'])) {
                return $item['thumbnailUrl'];
            }
        }

        return null;
    }
}
