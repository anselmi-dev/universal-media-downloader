<?php

return [
    'twitter_invalid_url' => 'Impossible d\'extraire l\'ID du tweet depuis l\'URL fournie.',
    'twitter_fetch_failed' => 'Impossible de récupérer le tweet. Vérifiez l\'URL et réessayez.',
    'post_deleted' => 'Cette publication a été supprimée ou n\'est plus disponible.',
    'tweet_not_found' => 'Tweet introuvable. Il a peut-être été supprimé ou l\'URL est incorrecte.',
    'tiktok_invalid_url' => 'Impossible d\'extraire l\'ID de la vidéo depuis l\'URL TikTok.',
    'tiktok_page_failed' => 'Impossible de charger la page TikTok. La vidéo peut être privée ou indisponible.',
    'tiktok_extract_failed' => 'Impossible d\'extraire les données de la vidéo. TikTok a peut-être modifié la structure de sa page. Réessayez plus tard.',
    'tiktok_parse_failed' => 'Impossible d\'interpréter les données de la page TikTok.',
    'video_not_found' => 'Vidéo introuvable ou indisponible.',
    'video_private' => 'Cette vidéo est privée ou indisponible.',
    'tiktok_extract_info_failed' => 'Impossible d\'extraire les informations de la vidéo depuis cette page.',
    'instagram_invalid_url'          => 'Impossible d\'extraire l\'ID de la publication depuis l\'URL Instagram.',
    'instagram_extract_failed'       => 'Impossible d\'extraire le contenu de cette publication Instagram. La publication peut être privée ou Instagram bloque la requête.',
    'instagram_story_no_session'     => 'Les stories et highlights Instagram nécessitent une authentification. Ajoutez votre INSTAGRAM_SESSION_ID dans le fichier .env pour activer cette fonction.',
    'instagram_story_requires_auth'  => 'Impossible de télécharger cette story ou highlight. Le cookie de session a peut-être expiré — mettez à jour INSTAGRAM_SESSION_ID dans .env.',
    'reddit_fetch_failed'            => 'Impossible de récupérer la publication Reddit. Elle a peut-être été supprimée ou est privée.',
    'reddit_no_media'                => 'Aucun média téléchargeable trouvé dans cette publication Reddit. Il peut s\'agir d\'un post texte, d\'un lien externe ou d\'un format non pris en charge.',
    'platform_coming_soon'           => 'Le support de :platform arrive bientôt. Restez à l\'écoute !',
    'unsupported_platform'           => 'Plateforme non prise en charge. Actuellement : X / Twitter, TikTok, Instagram, Reddit. YouTube Shorts bientôt.',
];
