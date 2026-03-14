<?php

return [
    'twitter_invalid_url' => 'Die Tweet-ID konnte aus der angegebenen URL nicht extrahiert werden.',
    'twitter_fetch_failed' => 'Der Tweet konnte nicht geladen werden. Bitte überprüfe die URL und versuche es erneut.',
    'post_deleted' => 'Dieser Beitrag wurde gelöscht oder ist nicht mehr verfügbar.',
    'tweet_not_found' => 'Tweet nicht gefunden. Er wurde möglicherweise gelöscht oder die URL ist falsch.',
    'tiktok_invalid_url' => 'Die Video-ID konnte aus der TikTok-URL nicht extrahiert werden.',
    'tiktok_page_failed' => 'Die TikTok-Seite konnte nicht geladen werden. Das Video ist möglicherweise privat oder nicht verfügbar.',
    'tiktok_extract_failed' => 'Die Videodaten konnten nicht extrahiert werden. TikTok hat möglicherweise die Seitenstruktur geändert. Versuche es später erneut.',
    'tiktok_parse_failed' => 'Die TikTok-Seitendaten konnten nicht gelesen werden.',
    'video_not_found' => 'Video nicht gefunden oder nicht verfügbar.',
    'video_private' => 'Dieses Video ist privat oder nicht verfügbar.',
    'tiktok_extract_info_failed' => 'Die Videoinformationen konnten von dieser Seite nicht extrahiert werden.',
    'instagram_invalid_url'          => 'Die Beitrags-ID konnte aus der Instagram-URL nicht extrahiert werden.',
    'instagram_extract_failed'       => 'Der Inhalt dieses Instagram-Beitrags konnte nicht extrahiert werden. Der Beitrag ist möglicherweise privat oder Instagram blockiert die Anfrage.',
    'instagram_story_no_session'     => 'Instagram-Stories und Highlights erfordern eine Authentifizierung. Füge deine INSTAGRAM_SESSION_ID in der .env-Datei hinzu.',
    'instagram_story_requires_auth'  => 'Diese Story oder dieses Highlight konnte nicht heruntergeladen werden. Das Session-Cookie ist möglicherweise abgelaufen — aktualisiere INSTAGRAM_SESSION_ID in .env.',
    'reddit_fetch_failed'            => 'Der Reddit-Beitrag konnte nicht geladen werden. Er wurde möglicherweise gelöscht oder ist privat.',
    'reddit_no_media'                => 'Keine herunterladbaren Medien in diesem Reddit-Beitrag gefunden. Es könnte ein Textbeitrag, ein externer Link oder ein nicht unterstütztes Format sein.',
    'platform_coming_soon'           => ':platform-Unterstützung kommt demnächst. Bleib dran!',
    'unsupported_platform'           => 'Nicht unterstützte Plattform. Aktuell unterstützt: X / Twitter, TikTok, Instagram, Reddit. YouTube Shorts demnächst.',
];
