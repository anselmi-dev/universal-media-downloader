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
        'title' => 'Anselmi | Download X, TikTok & Instagram',
        'description' => 'Download videos, photos, reels, stories and galleries from public X/Twitter, TikTok, Instagram and Reddit posts. Fast, free, no sign-up.',
      ),
      'es' => 
      array (
        'title' => 'Anselmi | Descarga X, TikTok e Instagram',
        'description' => 'Descarga videos, fotos, reels, historias y galerías de publicaciones públicas de X/Twitter, TikTok, Instagram y Reddit. Rápido, gratis, sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Anselmi | Télécharger X, TikTok, Instagram',
        'description' => 'Téléchargez vidéos, photos, reels, stories et galeries depuis X/Twitter, TikTok, Instagram et Reddit. Rapide, gratuit, sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'Anselmi | X, TikTok & Instagram laden',
        'description' => 'Lade Videos, Fotos, Reels, Stories und Galerien von X/Twitter, TikTok, Instagram und Reddit herunter. Schnell, kostenlos, ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Anselmi | Baixar X, TikTok e Instagram',
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
        'title' => 'Anselmi | Download X, TikTok & Instagram',
        'description' => 'Download videos, photos, reels, stories and galleries from public X/Twitter, TikTok, Instagram and Reddit posts. Fast, free, no sign-up.',
      ),
      'es' => 
      array (
        'title' => 'Anselmi | Descarga X, TikTok e Instagram',
        'description' => 'Descarga videos, fotos, reels, historias y galerías de publicaciones públicas de X/Twitter, TikTok, Instagram y Reddit. Rápido, gratis, sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Anselmi | Télécharger X, TikTok, Instagram',
        'description' => 'Téléchargez vidéos, photos, reels, stories et galeries depuis X/Twitter, TikTok, Instagram et Reddit. Rapide, gratuit, sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'Anselmi | X, TikTok & Instagram laden',
        'description' => 'Lade Videos, Fotos, Reels, Stories und Galerien von X/Twitter, TikTok, Instagram und Reddit herunter. Schnell, kostenlos, ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Anselmi | Baixar X, TikTok e Instagram',
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
        'title' => 'Anselmi | Download X, TikTok & Instagram',
        'description' => 'Download videos, photos, reels, stories and galleries from public X/Twitter, TikTok, Instagram and Reddit posts. Fast, free, no sign-up.',
      ),
      'es' => 
      array (
        'title' => 'Anselmi | Descarga X, TikTok e Instagram',
        'description' => 'Descarga videos, fotos, reels, historias y galerías de publicaciones públicas de X/Twitter, TikTok, Instagram y Reddit. Rápido, gratis, sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Anselmi | Télécharger X, TikTok, Instagram',
        'description' => 'Téléchargez vidéos, photos, reels, stories et galeries depuis X/Twitter, TikTok, Instagram et Reddit. Rapide, gratuit, sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'Anselmi | X, TikTok & Instagram laden',
        'description' => 'Lade Videos, Fotos, Reels, Stories und Galerien von X/Twitter, TikTok, Instagram und Reddit herunter. Schnell, kostenlos, ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Anselmi | Baixar X, TikTok e Instagram',
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
        'title' => 'Free X / Twitter Video Downloader | Anselmi',
        'description' => 'Download videos and photos from any public X (Twitter) post for free. Paste the tweet URL and save all media with one click. No sign-up. No watermark.',
      ),
      'es' => 
      array (
        'title' => 'Descargar videos de X / Twitter | Anselmi',
        'description' => 'Descarga videos y fotos de cualquier publicación pública de X (Twitter) gratis. Pega la URL del tweet y guarda todos los medios con un clic. Sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Télécharger vidéos Twitter / X | Anselmi',
        'description' => 'Téléchargez gratuitement vidéos et photos depuis tout tweet public X (Twitter). Collez l’URL et sauvegardez en un clic. Sans inscription. Sans filigrane.',
      ),
      'de' => 
      array (
        'title' => 'Twitter / X Videos laden | Anselmi',
        'description' => 'Lade Videos und Fotos aus öffentlichen X-(Twitter-)Beiträgen kostenlos herunter. Tweet-URL einfügen und alle Medien speichern. Ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Baixar vídeos do Twitter / X | Anselmi',
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
        'title' => 'Free X / Twitter Video Downloader | Anselmi',
        'description' => 'Download videos and photos from any public X (Twitter) post for free. Paste the tweet URL and save all media with one click. No sign-up. No watermark.',
      ),
      'es' => 
      array (
        'title' => 'Descargar videos de X / Twitter | Anselmi',
        'description' => 'Descarga videos y fotos de cualquier publicación pública de X (Twitter) gratis. Pega la URL del tweet y guarda todos los medios con un clic. Sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Télécharger vidéos Twitter / X | Anselmi',
        'description' => 'Téléchargez gratuitement vidéos et photos depuis tout tweet public X (Twitter). Collez l’URL et sauvegardez en un clic. Sans inscription. Sans filigrane.',
      ),
      'de' => 
      array (
        'title' => 'Twitter / X Videos laden | Anselmi',
        'description' => 'Lade Videos und Fotos aus öffentlichen X-(Twitter-)Beiträgen kostenlos herunter. Tweet-URL einfügen und alle Medien speichern. Ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Baixar vídeos do Twitter / X | Anselmi',
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
        'title' => 'Free Instagram Reels Downloader | Anselmi',
        'description' => 'Download Instagram videos, reels, photos, carousels, stories and highlights for free. Paste any public Instagram URL and save media instantly. No account needed.',
      ),
      'es' => 
      array (
        'title' => 'Descargar Reels de Instagram | Anselmi',
        'description' => 'Descarga videos, reels, fotos, carruseles, historias y destacados de Instagram gratis. Pega cualquier URL pública de Instagram y guarda medios al instante.',
      ),
      'fr' => 
      array (
        'title' => 'Télécharger Reels Instagram | Anselmi',
        'description' => 'Téléchargez gratuitement Reels, photos, carrousels, stories et highlights Instagram. Collez une URL publique et sauvegardez instantanément.',
      ),
      'de' => 
      array (
        'title' => 'Instagram Reels laden | Anselmi',
        'description' => 'Lade Instagram-Reels, Fotos, Karussells, Stories und Highlights kostenlos herunter. Öffentliche URL einfügen und Medien sofort speichern.',
      ),
      'pt' => 
      array (
        'title' => 'Baixar Reels do Instagram | Anselmi',
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
        'title' => 'Free Instagram Reels Downloader | Anselmi',
        'description' => 'Download Instagram videos, reels, photos, carousels, stories and highlights for free. Paste any public Instagram URL and save media instantly. No account needed.',
      ),
      'es' => 
      array (
        'title' => 'Descargar Reels de Instagram | Anselmi',
        'description' => 'Descarga videos, reels, fotos, carruseles, historias y destacados de Instagram gratis. Pega cualquier URL pública de Instagram y guarda medios al instante.',
      ),
      'fr' => 
      array (
        'title' => 'Télécharger Reels Instagram | Anselmi',
        'description' => 'Téléchargez gratuitement Reels, photos, carrousels, stories et highlights Instagram. Collez une URL publique et sauvegardez instantanément.',
      ),
      'de' => 
      array (
        'title' => 'Instagram Reels laden | Anselmi',
        'description' => 'Lade Instagram-Reels, Fotos, Karussells, Stories und Highlights kostenlos herunter. Öffentliche URL einfügen und Medien sofort speichern.',
      ),
      'pt' => 
      array (
        'title' => 'Baixar Reels do Instagram | Anselmi',
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
        'title' => 'TikTok Downloader No Watermark | Anselmi',
        'description' => 'Download TikTok videos without watermark for free. Paste any TikTok URL to save the video and audio in HD quality. No sign-up required.',
      ),
      'es' => 
      array (
        'title' => 'Descargar TikTok sin marca de agua | Anselmi',
        'description' => 'Descarga videos de TikTok sin marca de agua gratis. Pega cualquier URL de TikTok para guardar el video en calidad HD. Sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Télécharger TikTok sans filigrane | Anselmi',
        'description' => 'Téléchargez des vidéos TikTok sans filigrane gratuitement en HD. Collez une URL TikTok pour sauvegarder vidéo et audio. Sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'TikTok ohne Wasserzeichen | Anselmi',
        'description' => 'TikTok-Videos ohne Wasserzeichen kostenlos in HD herunterladen. TikTok-URL einfügen und Video sowie Audio speichern. Ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Baixar TikTok sem marca d’água | Anselmi',
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
        'title' => 'TikTok Downloader No Watermark | Anselmi',
        'description' => 'Download TikTok videos without watermark for free. Paste any TikTok URL to save the video and audio in HD quality. No sign-up required.',
      ),
      'es' => 
      array (
        'title' => 'Descargar TikTok sin marca de agua | Anselmi',
        'description' => 'Descarga videos de TikTok sin marca de agua gratis. Pega cualquier URL de TikTok para guardar el video en calidad HD. Sin registro.',
      ),
      'fr' => 
      array (
        'title' => 'Télécharger TikTok sans filigrane | Anselmi',
        'description' => 'Téléchargez des vidéos TikTok sans filigrane gratuitement en HD. Collez une URL TikTok pour sauvegarder vidéo et audio. Sans inscription.',
      ),
      'de' => 
      array (
        'title' => 'TikTok ohne Wasserzeichen | Anselmi',
        'description' => 'TikTok-Videos ohne Wasserzeichen kostenlos in HD herunterladen. TikTok-URL einfügen und Video sowie Audio speichern. Ohne Anmeldung.',
      ),
      'pt' => 
      array (
        'title' => 'Baixar TikTok sem marca d’água | Anselmi',
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
        'title' => 'Free Reddit Video Downloader | Anselmi',
        'description' => 'Download Reddit videos, GIFs and gallery images for free. Paste any Reddit post URL to save all media instantly. No account. No watermark.',
      ),
      'es' => 
      array (
        'title' => 'Descargar videos de Reddit | Anselmi',
        'description' => 'Descarga videos, GIFs e imágenes de galerías de Reddit gratis. Pega cualquier URL de publicación de Reddit para guardar todos los medios al instante.',
      ),
      'fr' => 
      array (
        'title' => 'Télécharger vidéos Reddit | Anselmi',
        'description' => 'Téléchargez gratuitement vidéos Reddit, GIFs et images de galerie. Collez une URL Reddit publique. Sans compte. Sans filigrane.',
      ),
      'de' => 
      array (
        'title' => 'Reddit Videos laden | Anselmi',
        'description' => 'Reddit-Videos, GIFs und Galeriebilder kostenlos herunterladen. Öffentliche Reddit-URL einfügen. Ohne Konto. Ohne Wasserzeichen.',
      ),
      'pt' => 
      array (
        'title' => 'Baixar vídeos do Reddit | Anselmi',
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
        'title' => 'Free Reddit Video Downloader | Anselmi',
        'description' => 'Download Reddit videos, GIFs and gallery images for free. Paste any Reddit post URL to save all media instantly. No account. No watermark.',
      ),
      'es' => 
      array (
        'title' => 'Descargar videos de Reddit | Anselmi',
        'description' => 'Descarga videos, GIFs e imágenes de galerías de Reddit gratis. Pega cualquier URL de publicación de Reddit para guardar todos los medios al instante.',
      ),
      'fr' => 
      array (
        'title' => 'Télécharger vidéos Reddit | Anselmi',
        'description' => 'Téléchargez gratuitement vidéos Reddit, GIFs et images de galerie. Collez une URL Reddit publique. Sans compte. Sans filigrane.',
      ),
      'de' => 
      array (
        'title' => 'Reddit Videos laden | Anselmi',
        'description' => 'Reddit-Videos, GIFs und Galeriebilder kostenlos herunterladen. Öffentliche Reddit-URL einfügen. Ohne Konto. Ohne Wasserzeichen.',
      ),
      'pt' => 
      array (
        'title' => 'Baixar vídeos do Reddit | Anselmi',
        'description' => 'Baixe vídeos, GIFs e imagens de galeria do Reddit de graça. Cole qualquer URL pública do Reddit. Sem conta. Sem marca d’água.',
      ),
    ),
  ),
);
