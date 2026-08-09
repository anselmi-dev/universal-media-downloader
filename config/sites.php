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

return array (
  'default' => 
  array (
    'name' => 'Anselmi Media Downloader',
    'platforms' => NULL,
    'placeholder' => 'https://x.com/username/status/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Anselmi Media Downloader | Download X, TikTok, Instagram & Reddit Media',
        'description' => 'Download videos, photos, reels, stories and galleries from public X/Twitter, TikTok, Instagram and Reddit posts. Fast, free, no sign-up.',
      ),
      'es' => 
      array (
        'title' => 'Anselmi Media Downloader | Descarga X, TikTok, Instagram y Reddit',
        'description' => 'Descarga videos, fotos, reels, historias y galerías de publicaciones públicas de X/Twitter, TikTok, Instagram y Reddit. Rápido, gratis, sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Anselmi Media Downloader | Télécharger X, TikTok, Instagram & Reddit',
        'description' => 'Téléchargez vidéos, photos, reels, stories et galeries depuis X/Twitter, TikTok, Instagram et Reddit. Rapide, gratuit, sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'Anselmi Media Downloader | X, TikTok, Instagram & Reddit herunterladen',
        'description' => 'Lade Videos, Fotos, Reels, Stories und Galerien von X/Twitter, TikTok, Instagram und Reddit herunter. Schnell, kostenlos, ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Anselmi Media Downloader | Baixar X, TikTok, Instagram e Reddit',
        'description' => 'Baixe vídeos, fotos, reels, stories e galerias do X/Twitter, TikTok, Instagram e Reddit. Rápido, grátis, sem cadastro.',
      ),
    ),
  ),
  'universal-media-downloader.anselmidev.on' => 
  array (
    'name' => 'Anselmi Media Downloader',
    'platforms' => NULL,
    'placeholder' => 'https://x.com/username/status/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Anselmi Media Downloader | Download X, TikTok, Instagram & Reddit Media',
        'description' => 'Download videos, photos, reels, stories and galleries from public X/Twitter, TikTok, Instagram and Reddit posts. Fast, free, no sign-up.',
      ),
      'es' => 
      array (
        'title' => 'Anselmi Media Downloader | Descarga X, TikTok, Instagram y Reddit',
        'description' => 'Descarga videos, fotos, reels, historias y galerías de publicaciones públicas de X/Twitter, TikTok, Instagram y Reddit. Rápido, gratis, sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Anselmi Media Downloader | Télécharger X, TikTok, Instagram & Reddit',
        'description' => 'Téléchargez vidéos, photos, reels, stories et galeries depuis X/Twitter, TikTok, Instagram et Reddit. Rapide, gratuit, sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'Anselmi Media Downloader | X, TikTok, Instagram & Reddit herunterladen',
        'description' => 'Lade Videos, Fotos, Reels, Stories und Galerien von X/Twitter, TikTok, Instagram und Reddit herunter. Schnell, kostenlos, ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Anselmi Media Downloader | Baixar X, TikTok, Instagram e Reddit',
        'description' => 'Baixe vídeos, fotos, reels, stories e galerias do X/Twitter, TikTok, Instagram e Reddit. Rápido, grátis, sem cadastro.',
      ),
    ),
  ),
  'social-media.anselmidev.com' => 
  array (
    'name' => 'Anselmi Media Downloader',
    'platforms' => NULL,
    'placeholder' => 'https://x.com/username/status/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Anselmi Media Downloader | Download X, TikTok, Instagram & Reddit Media',
        'description' => 'Download videos, photos, reels, stories and galleries from public X/Twitter, TikTok, Instagram and Reddit posts. Fast, free, no sign-up.',
      ),
      'es' => 
      array (
        'title' => 'Anselmi Media Downloader | Descarga X, TikTok, Instagram y Reddit',
        'description' => 'Descarga videos, fotos, reels, historias y galerías de publicaciones públicas de X/Twitter, TikTok, Instagram y Reddit. Rápido, gratis, sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Anselmi Media Downloader | Télécharger X, TikTok, Instagram & Reddit',
        'description' => 'Téléchargez vidéos, photos, reels, stories et galeries depuis X/Twitter, TikTok, Instagram et Reddit. Rapide, gratuit, sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'Anselmi Media Downloader | X, TikTok, Instagram & Reddit herunterladen',
        'description' => 'Lade Videos, Fotos, Reels, Stories und Galerien von X/Twitter, TikTok, Instagram und Reddit herunter. Schnell, kostenlos, ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Anselmi Media Downloader | Baixar X, TikTok, Instagram e Reddit',
        'description' => 'Baixe vídeos, fotos, reels, stories e galerias do X/Twitter, TikTok, Instagram e Reddit. Rápido, grátis, sem cadastro.',
      ),
    ),
  ),
  'twitter-downloader.anselmidev.on' => 
  array (
    'name' => 'Twitter / X Downloader',
    'platforms' => 
    array (
      0 => 'Twitter',
    ),
    'placeholder' => 'https://x.com/username/status/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Free Twitter / X Video & Photo Downloader — Save Tweets Instantly',
        'description' => 'Download videos and photos from any public X (Twitter) post for free. Paste the tweet URL and save all media with one click. No sign-up. No watermark.',
      ),
      'es' => 
      array (
        'title' => 'Descargador Gratis de Videos y Fotos de Twitter / X',
        'description' => 'Descarga videos y fotos de cualquier publicación pública de X (Twitter) gratis. Pega la URL del tweet y guarda todos los medios con un clic. Sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Téléchargeur Gratuit Twitter / X — Vidéos et Photos',
        'description' => 'Téléchargez gratuitement vidéos et photos depuis tout tweet public X (Twitter). Collez l’URL et sauvegardez en un clic. Sans inscription. Sans filigrane.',
      ),
      'de' => 
      array (
        'title' => 'Kostenloser Twitter / X Downloader — Videos & Fotos',
        'description' => 'Lade Videos und Fotos aus öffentlichen X-(Twitter-)Beiträgen kostenlos herunter. Tweet-URL einfügen und alle Medien speichern. Ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Baixador Grátis Twitter / X — Vídeos e Fotos',
        'description' => 'Baixe vídeos e fotos de qualquer post público do X (Twitter) de graça. Cole a URL do tweet e salve com um clique. Sem cadastro.',
      ),
    ),
  ),
  'twitter-downloader.anselmidev.com' => 
  array (
    'name' => 'Twitter / X Downloader',
    'platforms' => 
    array (
      0 => 'Twitter',
    ),
    'placeholder' => 'https://x.com/username/status/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Free Twitter / X Video & Photo Downloader — Save Tweets Instantly',
        'description' => 'Download videos and photos from any public X (Twitter) post for free. Paste the tweet URL and save all media with one click. No sign-up. No watermark.',
      ),
      'es' => 
      array (
        'title' => 'Descargador Gratis de Videos y Fotos de Twitter / X',
        'description' => 'Descarga videos y fotos de cualquier publicación pública de X (Twitter) gratis. Pega la URL del tweet y guarda todos los medios con un clic. Sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Téléchargeur Gratuit Twitter / X — Vidéos et Photos',
        'description' => 'Téléchargez gratuitement vidéos et photos depuis tout tweet public X (Twitter). Collez l’URL et sauvegardez en un clic. Sans inscription. Sans filigrane.',
      ),
      'de' => 
      array (
        'title' => 'Kostenloser Twitter / X Downloader — Videos & Fotos',
        'description' => 'Lade Videos und Fotos aus öffentlichen X-(Twitter-)Beiträgen kostenlos herunter. Tweet-URL einfügen und alle Medien speichern. Ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Baixador Grátis Twitter / X — Vídeos e Fotos',
        'description' => 'Baixe vídeos e fotos de qualquer post público do X (Twitter) de graça. Cole a URL do tweet e salve com um clique. Sem cadastro.',
      ),
    ),
  ),
  'instagram-downloader.anselmidev.on' => 
  array (
    'name' => 'Instagram Downloader',
    'platforms' => 
    array (
      0 => 'Instagram',
    ),
    'placeholder' => 'https://www.instagram.com/p/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Free Instagram Downloader — Save Reels, Photos & Stories',
        'description' => 'Download Instagram videos, reels, photos, carousels, stories and highlights for free. Paste any public Instagram URL and save media instantly. No account needed.',
      ),
      'es' => 
      array (
        'title' => 'Descargador Gratis de Instagram — Guarda Reels, Fotos e Historias',
        'description' => 'Descarga videos, reels, fotos, carruseles, historias y destacados de Instagram gratis. Pega cualquier URL pública de Instagram y guarda medios al instante.',
      ),
      'fr' => 
      array (
        'title' => 'Téléchargeur Instagram Gratuit — Reels, Photos & Stories',
        'description' => 'Téléchargez gratuitement Reels, photos, carrousels, stories et highlights Instagram. Collez une URL publique et sauvegardez instantanément.',
      ),
      'de' => 
      array (
        'title' => 'Kostenloser Instagram Downloader — Reels, Fotos & Stories',
        'description' => 'Lade Instagram-Reels, Fotos, Karussells, Stories und Highlights kostenlos herunter. Öffentliche URL einfügen und Medien sofort speichern.',
      ),
      'pt' => 
      array (
        'title' => 'Baixador Grátis de Instagram — Reels, Fotos e Stories',
        'description' => 'Baixe Reels, fotos, carrosséis, stories e destaques do Instagram de graça. Cole qualquer URL pública e salve na hora.',
      ),
    ),
  ),
  'instagram-downloader.anselmidev.com' => 
  array (
    'name' => 'Instagram Downloader',
    'platforms' => 
    array (
      0 => 'Instagram',
    ),
    'placeholder' => 'https://www.instagram.com/p/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Free Instagram Downloader — Save Reels, Photos & Stories',
        'description' => 'Download Instagram videos, reels, photos, carousels, stories and highlights for free. Paste any public Instagram URL and save media instantly. No account needed.',
      ),
      'es' => 
      array (
        'title' => 'Descargador Gratis de Instagram — Guarda Reels, Fotos e Historias',
        'description' => 'Descarga videos, reels, fotos, carruseles, historias y destacados de Instagram gratis. Pega cualquier URL pública de Instagram y guarda medios al instante.',
      ),
      'fr' => 
      array (
        'title' => 'Téléchargeur Instagram Gratuit — Reels, Photos & Stories',
        'description' => 'Téléchargez gratuitement Reels, photos, carrousels, stories et highlights Instagram. Collez une URL publique et sauvegardez instantanément.',
      ),
      'de' => 
      array (
        'title' => 'Kostenloser Instagram Downloader — Reels, Fotos & Stories',
        'description' => 'Lade Instagram-Reels, Fotos, Karussells, Stories und Highlights kostenlos herunter. Öffentliche URL einfügen und Medien sofort speichern.',
      ),
      'pt' => 
      array (
        'title' => 'Baixador Grátis de Instagram — Reels, Fotos e Stories',
        'description' => 'Baixe Reels, fotos, carrosséis, stories e destaques do Instagram de graça. Cole qualquer URL pública e salve na hora.',
      ),
    ),
  ),
  'tiktok-downloader.anselmidev.on' => 
  array (
    'name' => 'TikTok Downloader',
    'platforms' => 
    array (
      0 => 'TikTok',
    ),
    'placeholder' => 'https://www.tiktok.com/@username/video/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Free TikTok Downloader — Save Videos Without Watermark',
        'description' => 'Download TikTok videos without watermark for free. Paste any TikTok URL to save the video and audio in HD quality. No sign-up required.',
      ),
      'es' => 
      array (
        'title' => 'Descargador Gratis de TikTok — Videos Sin Marca de Agua',
        'description' => 'Descarga videos de TikTok sin marca de agua gratis. Pega cualquier URL de TikTok para guardar el video en calidad HD. Sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Téléchargeur TikTok Gratuit — Sans Filigrane',
        'description' => 'Téléchargez des vidéos TikTok sans filigrane gratuitement en HD. Collez une URL TikTok pour sauvegarder vidéo et audio. Sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'Kostenloser TikTok Downloader — Ohne Wasserzeichen',
        'description' => 'TikTok-Videos ohne Wasserzeichen kostenlos in HD herunterladen. TikTok-URL einfügen und Video sowie Audio speichern. Ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Baixador Grátis de TikTok — Sem Marca d’Água',
        'description' => 'Baixe vídeos do TikTok sem marca d’água grátis em HD. Cole qualquer URL do TikTok para salvar vídeo e áudio. Sem cadastro.',
      ),
    ),
  ),
  'tiktok-downloader.anselmidev.com' => 
  array (
    'name' => 'TikTok Downloader',
    'platforms' => 
    array (
      0 => 'TikTok',
    ),
    'placeholder' => 'https://www.tiktok.com/@username/video/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Free TikTok Downloader — Save Videos Without Watermark',
        'description' => 'Download TikTok videos without watermark for free. Paste any TikTok URL to save the video and audio in HD quality. No sign-up required.',
      ),
      'es' => 
      array (
        'title' => 'Descargador Gratis de TikTok — Videos Sin Marca de Agua',
        'description' => 'Descarga videos de TikTok sin marca de agua gratis. Pega cualquier URL de TikTok para guardar el video en calidad HD. Sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Téléchargeur TikTok Gratuit — Sans Filigrane',
        'description' => 'Téléchargez des vidéos TikTok sans filigrane gratuitement en HD. Collez une URL TikTok pour sauvegarder vidéo et audio. Sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'Kostenloser TikTok Downloader — Ohne Wasserzeichen',
        'description' => 'TikTok-Videos ohne Wasserzeichen kostenlos in HD herunterladen. TikTok-URL einfügen und Video sowie Audio speichern. Ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Baixador Grátis de TikTok — Sem Marca d’Água',
        'description' => 'Baixe vídeos do TikTok sem marca d’água grátis em HD. Cole qualquer URL do TikTok para salvar vídeo e áudio. Sem cadastro.',
      ),
    ),
  ),
  'reddit-downloader.anselmidev.on' => 
  array (
    'name' => 'Reddit Downloader',
    'platforms' => 
    array (
      0 => 'Reddit',
    ),
    'placeholder' => 'https://www.reddit.com/r/sub/comments/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Free Reddit Downloader — Save Videos, GIFs & Gallery Images',
        'description' => 'Download Reddit videos, GIFs and gallery images for free. Paste any Reddit post URL to save all media instantly. No account. No watermark.',
      ),
      'es' => 
      array (
        'title' => 'Descargador Gratis de Reddit — Guarda Videos, GIFs e Imágenes',
        'description' => 'Descarga videos, GIFs e imágenes de galerías de Reddit gratis. Pega cualquier URL de publicación de Reddit para guardar todos los medios al instante.',
      ),
      'fr' => 
      array (
        'title' => 'Téléchargeur Reddit Gratuit — Vidéos, GIFs & Galeries',
        'description' => 'Téléchargez gratuitement vidéos Reddit, GIFs et images de galerie. Collez une URL Reddit publique. Sans compte. Sans filigrane.',
      ),
      'de' => 
      array (
        'title' => 'Kostenloser Reddit Downloader — Videos, GIFs & Galerien',
        'description' => 'Reddit-Videos, GIFs und Galeriebilder kostenlos herunterladen. Öffentliche Reddit-URL einfügen. Ohne Konto. Ohne Wasserzeichen.',
      ),
      'pt' => 
      array (
        'title' => 'Baixador Grátis de Reddit — Vídeos, GIFs e Galerias',
        'description' => 'Baixe vídeos, GIFs e imagens de galeria do Reddit de graça. Cole qualquer URL pública do Reddit. Sem conta. Sem marca d’água.',
      ),
    ),
  ),
  'reddit-downloader.anselmidev.com' => 
  array (
    'name' => 'Reddit Downloader',
    'platforms' => 
    array (
      0 => 'Reddit',
    ),
    'placeholder' => 'https://www.reddit.com/r/sub/comments/...',
    'seo' => 
    array (
      'en' => 
      array (
        'title' => 'Free Reddit Downloader — Save Videos, GIFs & Gallery Images',
        'description' => 'Download Reddit videos, GIFs and gallery images for free. Paste any Reddit post URL to save all media instantly. No account. No watermark.',
      ),
      'es' => 
      array (
        'title' => 'Descargador Gratis de Reddit — Guarda Videos, GIFs e Imágenes',
        'description' => 'Descarga videos, GIFs e imágenes de galerías de Reddit gratis. Pega cualquier URL de publicación de Reddit para guardar todos los medios al instante.',
      ),
      'fr' => 
      array (
        'title' => 'Téléchargeur Reddit Gratuit — Vidéos, GIFs & Galeries',
        'description' => 'Téléchargez gratuitement vidéos Reddit, GIFs et images de galerie. Collez une URL Reddit publique. Sans compte. Sans filigrane.',
      ),
      'de' => 
      array (
        'title' => 'Kostenloser Reddit Downloader — Videos, GIFs & Galerien',
        'description' => 'Reddit-Videos, GIFs und Galeriebilder kostenlos herunterladen. Öffentliche Reddit-URL einfügen. Ohne Konto. Ohne Wasserzeichen.',
      ),
      'pt' => 
      array (
        'title' => 'Baixador Grátis de Reddit — Vídeos, GIFs e Galerias',
        'description' => 'Baixe vídeos, GIFs e imagens de galeria do Reddit de graça. Cole qualquer URL pública do Reddit. Sem conta. Sem marca d’água.',
      ),
    ),
  ),
);
