<?php

/**
 * Platform-specific landing pages for SEO.
 * Each slug maps to a platform config used when rendering /{slug}.
 * SEO titles use "Anselmi Media Downloader" for brand consistency.
 */

$base = [
    'x-twitter-video-downloader' => [
        'platform'   => 'Twitter',
        'placeholder' => 'https://x.com/username/status/...',
        'seo' => [
            'en' => [
                'title'       => 'X/Twitter Video Downloader | Anselmi Media Downloader',
                'description' => 'Download videos and photos from any public X (Twitter) post for free. Paste the tweet URL and save all media with one click. No sign-up. No watermark.',
            ],
            'es' => [
                'title'       => 'Descargador de Videos de X/Twitter | Anselmi Media Downloader',
                'description' => 'Descarga videos y fotos de cualquier publicación pública de X (Twitter) gratis. Pega la URL del tweet y guarda todos los medios con un clic. Sin registro.',
            ],
        ],
    ],
    'tiktok-video-downloader' => [
        'platform'   => 'TikTok',
        'placeholder' => 'https://www.tiktok.com/@username/video/...',
        'seo' => [
            'en' => [
                'title'       => 'TikTok Video Downloader | Anselmi Media Downloader',
                'description' => 'Download TikTok videos without watermark for free. Paste any TikTok URL to save the video and audio in HD quality. No sign-up required.',
            ],
            'es' => [
                'title'       => 'Descargador de Videos de TikTok | Anselmi Media Downloader',
                'description' => 'Descarga videos de TikTok sin marca de agua gratis. Pega cualquier URL de TikTok para guardar el video en calidad HD. Sin registro.',
            ],
        ],
    ],
    'instagram-downloader' => [
        'platform'   => 'Instagram',
        'placeholder' => 'https://www.instagram.com/p/...',
        'seo' => [
            'en' => [
                'title'       => 'Instagram Downloader | Anselmi Media Downloader',
                'description' => 'Download Instagram videos, reels, photos, carousels, stories and highlights for free. Paste any public Instagram URL and save media instantly. No account needed.',
            ],
            'es' => [
                'title'       => 'Descargador de Instagram | Anselmi Media Downloader',
                'description' => 'Descarga videos, reels, fotos, carruseles, historias y destacados de Instagram gratis. Pega cualquier URL pública de Instagram y guarda medios al instante.',
            ],
        ],
    ],
    'reddit-video-downloader' => [
        'platform'   => 'Reddit',
        'placeholder' => 'https://www.reddit.com/r/sub/comments/...',
        'seo' => [
            'en' => [
                'title'       => 'Reddit Video Downloader | Anselmi Media Downloader',
                'description' => 'Download Reddit videos, GIFs and gallery images for free. Paste any Reddit post URL to save all media instantly. No account. No watermark.',
            ],
            'es' => [
                'title'       => 'Descargador de Videos de Reddit | Anselmi Media Downloader',
                'description' => 'Descarga videos, GIFs e imágenes de galerías de Reddit gratis. Pega cualquier URL de publicación de Reddit para guardar todos los medios al instante.',
            ],
        ],
    ],
];

return $base;
