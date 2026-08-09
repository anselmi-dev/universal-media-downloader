<?php

/**
 * Platform-specific landing pages for SEO.
 * Each slug maps to a platform config used when rendering /{slug}.
 */

return [
    'x-twitter-video-downloader' => [
        'platform' => 'Twitter',
        'placeholder' => 'https://x.com/username/status/...',
        'seo' => [
            'en' => [
                'title' => 'Download X / Twitter Videos MP4 | Anselmi',
                'description' => 'Free Twitter / X video downloader. Paste any public tweet URL to save videos and photos in MP4/HD. No sign-up. No watermark. Works on phone and desktop.',
            ],
            'es' => [
                'title' => 'Descargar videos de X MP4 | Anselmi',
                'description' => 'Descargador gratis de videos de Twitter / X. Pega la URL de un tweet público y guarda videos y fotos en MP4/HD. Sin registro. Sin marca de agua.',
            ],
            'fr' => [
                'title' => 'Télécharger vidéos X MP4 | Anselmi',
                'description' => 'Téléchargeur gratuit de vidéos Twitter / X. Collez l’URL d’un tweet public pour sauvegarder vidéos et photos en MP4/HD. Sans inscription. Sans filigrane.',
            ],
            'de' => [
                'title' => 'X Videos MP4 laden | Anselmi',
                'description' => 'Kostenloser Twitter-/X-Video-Downloader. Öffentliche Tweet-URL einfügen und Videos sowie Fotos in MP4/HD speichern. Ohne Anmeldung. Ohne Wasserzeichen.',
            ],
            'pt' => [
                'title' => 'Baixar vídeos do X MP4 | Anselmi',
                'description' => 'Baixador grátis de vídeos do Twitter / X. Cole a URL de um tweet público e salve vídeos e fotos em MP4/HD. Sem cadastro. Sem marca d’água.',
            ],
        ],
    ],
    'tiktok-video-downloader' => [
        'platform' => 'TikTok',
        'placeholder' => 'https://www.tiktok.com/@username/video/...',
        'seo' => [
            'en' => [
                'title' => 'TikTok No Watermark Downloader | Anselmi',
                'description' => 'Download TikTok videos without watermark for free in HD. Paste any public TikTok URL to save video or audio. No sign-up required.',
            ],
            'es' => [
                'title' => 'TikTok sin marca de agua | Anselmi',
                'description' => 'Descarga videos de TikTok sin marca de agua gratis en HD. Pega cualquier URL pública de TikTok para guardar video o audio. Sin registro.',
            ],
            'fr' => [
                'title' => 'TikTok sans filigrane | Anselmi',
                'description' => 'Téléchargez des vidéos TikTok sans filigrane gratuitement en HD. Collez une URL TikTok publique pour sauvegarder la vidéo ou l’audio. Sans inscription.',
            ],
            'de' => [
                'title' => 'TikTok ohne Wasserzeichen | Anselmi',
                'description' => 'TikTok-Videos kostenlos ohne Wasserzeichen in HD herunterladen. Öffentliche TikTok-URL einfügen und Video oder Audio speichern. Ohne Anmeldung.',
            ],
            'pt' => [
                'title' => 'TikTok sem marca d’água | Anselmi',
                'description' => 'Baixe vídeos do TikTok sem marca d’água grátis em HD. Cole qualquer URL pública do TikTok para salvar vídeo ou áudio. Sem cadastro.',
            ],
        ],
    ],
    'instagram-downloader' => [
        'platform' => 'Instagram',
        'placeholder' => 'https://www.instagram.com/p/...',
        'seo' => [
            'en' => [
                'title' => 'Instagram Reels Downloader | Anselmi',
                'description' => 'Free Instagram downloader for Reels, photos, carousels, stories and highlights. Paste any public Instagram URL and save media instantly. No account needed.',
            ],
            'es' => [
                'title' => 'Descargar Instagram Reels | Anselmi',
                'description' => 'Descargador gratis de Instagram para Reels, fotos, carruseles, historias y destacados. Pega cualquier URL pública y guarda medios al instante. Sin cuenta.',
            ],
            'fr' => [
                'title' => 'Télécharger Instagram Reels | Anselmi',
                'description' => 'Téléchargeur Instagram gratuit pour Reels, photos, carrousels, stories et highlights. Collez une URL publique et sauvegardez instantanément. Sans compte.',
            ],
            'de' => [
                'title' => 'Instagram Reels laden | Anselmi',
                'description' => 'Kostenloser Instagram-Downloader für Reels, Fotos, Karussells, Stories und Highlights. Öffentliche Instagram-URL einfügen und Medien sofort speichern.',
            ],
            'pt' => [
                'title' => 'Baixar Instagram Reels | Anselmi',
                'description' => 'Baixador grátis de Instagram para Reels, fotos, carrosséis, stories e destaques. Cole qualquer URL pública e salve mídia na hora. Sem conta.',
            ],
        ],
    ],
    'reddit-video-downloader' => [
        'platform' => 'Reddit',
        'placeholder' => 'https://www.reddit.com/r/sub/comments/...',
        'seo' => [
            'en' => [
                'title' => 'Reddit Video Downloader | Anselmi',
                'description' => 'Download Reddit videos with audio, GIFs and gallery images for free. Paste any public Reddit post URL. No account. No watermark.',
            ],
            'es' => [
                'title' => 'Descargar videos Reddit | Anselmi',
                'description' => 'Descarga videos de Reddit con audio, GIFs e imágenes de galería gratis. Pega cualquier URL pública de Reddit. Sin cuenta. Sin marca de agua.',
            ],
            'fr' => [
                'title' => 'Télécharger vidéos Reddit | Anselmi',
                'description' => 'Téléchargez des vidéos Reddit avec audio, GIFs et images de galerie gratuitement. Collez une URL Reddit publique. Sans compte. Sans filigrane.',
            ],
            'de' => [
                'title' => 'Reddit Videos laden | Anselmi',
                'description' => 'Reddit-Videos mit Audio, GIFs und Galeriebilder kostenlos herunterladen. Öffentliche Reddit-URL einfügen. Ohne Konto. Ohne Wasserzeichen.',
            ],
            'pt' => [
                'title' => 'Baixar vídeos Reddit | Anselmi',
                'description' => 'Baixe vídeos do Reddit com áudio, GIFs e imagens de galeria grátis. Cole qualquer URL pública do Reddit. Sem conta. Sem marca d’água.',
            ],
        ],
    ],
];
