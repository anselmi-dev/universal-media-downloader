<?php

return [
    'twitter_invalid_url' => 'No se pudo extraer el ID del tweet de la URL proporcionada.',
    'twitter_fetch_failed' => 'No se pudo obtener el tweet. Comprueba la URL e inténtalo de nuevo.',
    'post_deleted' => 'Esta publicación fue eliminada o ya no está disponible.',
    'tweet_not_found' => 'Tweet no encontrado. Puede haber sido eliminado o la URL es incorrecta.',
    'tiktok_invalid_url' => 'No se pudo extraer el ID del video de la URL de TikTok.',
    'tiktok_page_failed' => 'No se pudo cargar la página de TikTok. El video puede ser privado o no estar disponible.',
    'tiktok_extract_failed' => 'No se pudo extraer los datos del video. TikTok puede haber cambiado la estructura de su página. Inténtalo más tarde.',
    'tiktok_parse_failed' => 'No se pudo interpretar los datos de la página de TikTok.',
    'video_not_found' => 'Video no encontrado o no disponible.',
    'video_private' => 'Este video es privado o no está disponible.',
    'tiktok_extract_info_failed' => 'No se pudo extraer la información del video de esta página.',
    'instagram_invalid_url'          => 'No se pudo extraer el ID de la publicación de la URL de Instagram.',
    'instagram_extract_failed'       => 'No se pudo extraer el contenido de esta publicación de Instagram. La publicación puede ser privada o Instagram puede estar bloqueando la solicitud.',
    'instagram_story_no_session'     => 'Las historias y destacados de Instagram requieren autenticación. Agrega tu INSTAGRAM_SESSION_ID al archivo .env para habilitar esta función.',
    'instagram_story_requires_auth'  => 'No se pudo descargar la historia o el destacado. La cookie de sesión puede haber expirado — actualiza INSTAGRAM_SESSION_ID en .env.',
    'reddit_fetch_failed'            => 'No se pudo obtener la publicación de Reddit. Puede haber sido eliminada o ser privada.',
    'reddit_no_media'                => 'No se encontró contenido descargable en esta publicación de Reddit. Puede ser una publicación de texto, un enlace externo o un formato no compatible.',
    'platform_coming_soon'           => 'El soporte de :platform llegará pronto. ¡Mantente atento!',
    'unsupported_platform'           => 'Plataforma no soportada. Actualmente soportado: X / Twitter, TikTok, Instagram, Reddit. YouTube Shorts próximamente.',
];
