<?php

/**
 * Multi-site configuration.
 *
 * Each key is a hostname (without www). The middleware SetSite loads the
 * matching entry and stores it under config('site.*'), making it available
 * to the factory, layout, and views without touching the Livewire component.
 *
 * 'platforms' => null means "all supported platforms".
 */

$chip = static function (string $class): array {
    return ['class' => $class];
};

return [

    // ── Default / fallback ────────────────────────────────────────────────────
    // Used when the current hostname doesn't match any entry below.
    'default' => [
        'name'        => 'Social Media Downloader',
        'platforms'   => null, // null = all platforms enabled
        'placeholder' => 'https://x.com/username/status/...',
        'seo' => [
            'en' => [
                'title'       => 'Free Video & Photo Downloader — X/Twitter, TikTok, Instagram, Reddit',
                'description' => 'Download videos and photos from X (Twitter), TikTok, Instagram and Reddit for free. Paste any post URL to save media instantly. No watermark. No account needed.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de Videos y Fotos — X/Twitter, TikTok, Instagram, Reddit',
                'description' => 'Descarga videos y fotos de X (Twitter), TikTok, Instagram y Reddit gratis. Pega cualquier URL para guardar medios al instante. Sin marca de agua. Sin registro.',
            ],
        ],
    ],

    // ── All-in-one (local) ───────────────────────────────────────────────────
    'universal-media-downloader.anselmidev.on' => [
        'name'        => 'Social Media Downloader',
        'platforms'   => null,
        'placeholder' => 'https://x.com/username/status/...',
        'seo' => [
            'en' => [
                'title'       => 'Free Video & Photo Downloader — X/Twitter, TikTok, Instagram, Reddit',
                'description' => 'Download videos and photos from X (Twitter), TikTok, Instagram and Reddit for free. Paste any post URL to save media instantly. No watermark. No account needed.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de Videos y Fotos — X/Twitter, TikTok, Instagram, Reddit',
                'description' => 'Descarga videos y fotos de X (Twitter), TikTok, Instagram y Reddit gratis. Pega cualquier URL para guardar medios al instante. Sin marca de agua. Sin registro.',
            ],
        ],
    ],

    // ── All-in-one (production domain) ───────────────────────────────────────
    'social-media.anselmidev.com' => [
        'name'        => 'Social Media Downloader',
        'platforms'   => null,
        'placeholder' => 'https://x.com/username/status/...',
        'seo' => [
            'en' => [
                'title'       => 'Free Video & Photo Downloader — X/Twitter, TikTok, Instagram, Reddit',
                'description' => 'Download videos and photos from X (Twitter), TikTok, Instagram and Reddit for free. Paste any post URL to save media instantly. No watermark. No account needed.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de Videos y Fotos — X/Twitter, TikTok, Instagram, Reddit',
                'description' => 'Descarga videos y fotos de X (Twitter), TikTok, Instagram y Reddit gratis. Pega cualquier URL para guardar medios al instante. Sin marca de agua. Sin registro.',
            ],
        ],
    ],

    // ── Twitter / X only ─────────────────────────────────────────────────────
    'twitter-downloader.anselmidev.on' => [
        'name'        => 'Twitter / X Downloader',
        'platforms'   => ['Twitter'],
        'placeholder' => 'https://x.com/username/status/...',
        'seo' => [
            'en' => [
                'title'       => 'Free Twitter / X Video & Photo Downloader — Save Tweets Instantly',
                'description' => 'Download videos and photos from any public X (Twitter) post for free. Paste the tweet URL and save all media with one click. No sign-up. No watermark.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de Videos y Fotos de Twitter / X',
                'description' => 'Descarga videos y fotos de cualquier publicación pública de X (Twitter) gratis. Pega la URL del tweet y guarda todos los medios con un clic. Sin registro.',
            ],
        ],
    ],

    'twitter-downloader.anselmidev.com' => [
        'name'        => 'Twitter / X Downloader',
        'platforms'   => ['Twitter'],
        'placeholder' => 'https://x.com/username/status/...',
        'seo' => [
            'en' => [
                'title'       => 'Free Twitter / X Video & Photo Downloader — Save Tweets Instantly',
                'description' => 'Download videos and photos from any public X (Twitter) post for free. Paste the tweet URL and save all media with one click. No sign-up. No watermark.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de Videos y Fotos de Twitter / X',
                'description' => 'Descarga videos y fotos de cualquier publicación pública de X (Twitter) gratis. Pega la URL del tweet y guarda todos los medios con un clic. Sin registro.',
            ],
        ],
    ],

    // ── Instagram only ────────────────────────────────────────────────────────
    'instagram-downloader.anselmidev.on' => [
        'name'        => 'Instagram Downloader',
        'platforms'   => ['Instagram'],
        'placeholder' => 'https://www.instagram.com/p/...',
        'seo' => [
            'en' => [
                'title'       => 'Free Instagram Downloader — Save Reels, Photos & Stories',
                'description' => 'Download Instagram videos, reels, photos, carousels, stories and highlights for free. Paste any public Instagram URL and save media instantly. No account needed.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de Instagram — Guarda Reels, Fotos e Historias',
                'description' => 'Descarga videos, reels, fotos, carruseles, historias y destacados de Instagram gratis. Pega cualquier URL pública de Instagram y guarda medios al instante.',
            ],
        ],
    ],

    'instagram-downloader.anselmidev.com' => [
        'name'        => 'Instagram Downloader',
        'platforms'   => ['Instagram'],
        'placeholder' => 'https://www.instagram.com/p/...',
        'seo' => [
            'en' => [
                'title'       => 'Free Instagram Downloader — Save Reels, Photos & Stories',
                'description' => 'Download Instagram videos, reels, photos, carousels, stories and highlights for free. Paste any public Instagram URL and save media instantly. No account needed.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de Instagram — Guarda Reels, Fotos e Historias',
                'description' => 'Descarga videos, reels, fotos, carruseles, historias y destacados de Instagram gratis. Pega cualquier URL pública de Instagram y guarda medios al instante.',
            ],
        ],
    ],

    // ── TikTok only ───────────────────────────────────────────────────────────
    'tiktok-downloader.anselmidev.on' => [
        'name'        => 'TikTok Downloader',
        'platforms'   => ['TikTok'],
        'placeholder' => 'https://www.tiktok.com/@username/video/...',
        'seo' => [
            'en' => [
                'title'       => 'Free TikTok Downloader — Save Videos Without Watermark',
                'description' => 'Download TikTok videos without watermark for free. Paste any TikTok URL to save the video and audio in HD quality. No sign-up required.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de TikTok — Videos Sin Marca de Agua',
                'description' => 'Descarga videos de TikTok sin marca de agua gratis. Pega cualquier URL de TikTok para guardar el video en calidad HD. Sin registro.',
            ],
        ],
    ],

    'tiktok-downloader.anselmidev.com' => [
        'name'        => 'TikTok Downloader',
        'platforms'   => ['TikTok'],
        'placeholder' => 'https://www.tiktok.com/@username/video/...',
        'seo' => [
            'en' => [
                'title'       => 'Free TikTok Downloader — Save Videos Without Watermark',
                'description' => 'Download TikTok videos without watermark for free. Paste any TikTok URL to save the video and audio in HD quality. No sign-up required.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de TikTok — Videos Sin Marca de Agua',
                'description' => 'Descarga videos de TikTok sin marca de agua gratis. Pega cualquier URL de TikTok para guardar el video en calidad HD. Sin registro.',
            ],
        ],
    ],

    // ── Reddit only ───────────────────────────────────────────────────────────
    'reddit-downloader.anselmidev.on' => [
        'name'        => 'Reddit Downloader',
        'platforms'   => ['Reddit'],
        'placeholder' => 'https://www.reddit.com/r/sub/comments/...',
        'seo' => [
            'en' => [
                'title'       => 'Free Reddit Downloader — Save Videos, GIFs & Gallery Images',
                'description' => 'Download Reddit videos, GIFs and gallery images for free. Paste any Reddit post URL to save all media instantly. No account. No watermark.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de Reddit — Guarda Videos, GIFs e Imágenes',
                'description' => 'Descarga videos, GIFs e imágenes de galerías de Reddit gratis. Pega cualquier URL de publicación de Reddit para guardar todos los medios al instante.',
            ],
        ],
    ],

    'reddit-downloader.anselmidev.com' => [
        'name'        => 'Reddit Downloader',
        'platforms'   => ['Reddit'],
        'placeholder' => 'https://www.reddit.com/r/sub/comments/...',
        'seo' => [
            'en' => [
                'title'       => 'Free Reddit Downloader — Save Videos, GIFs & Gallery Images',
                'description' => 'Download Reddit videos, GIFs and gallery images for free. Paste any Reddit post URL to save all media instantly. No account. No watermark.',
            ],
            'es' => [
                'title'       => 'Descargador Gratis de Reddit — Guarda Videos, GIFs e Imágenes',
                'description' => 'Descarga videos, GIFs e imágenes de galerías de Reddit gratis. Pega cualquier URL de publicación de Reddit para guardar todos los medios al instante.',
            ],
        ],
    ],

];
