<?php

return [
    'twitter_invalid_url' => 'Não foi possível extrair o ID do tweet da URL fornecida.',
    'twitter_fetch_failed' => 'Não foi possível buscar o tweet. Verifique a URL e tente novamente.',
    'post_deleted' => 'Este post foi excluído ou não está mais disponível.',
    'tweet_not_found' => 'Tweet não encontrado. Pode ter sido excluído ou a URL está incorreta.',
    'tiktok_invalid_url' => 'Não foi possível extrair o ID do vídeo da URL do TikTok.',
    'tiktok_page_failed' => 'Não foi possível carregar a página do TikTok. O vídeo pode ser privado ou indisponível.',
    'tiktok_extract_failed' => 'Não foi possível extrair os dados do vídeo. O TikTok pode ter alterado a estrutura da página. Tente novamente mais tarde.',
    'tiktok_parse_failed' => 'Não foi possível interpretar os dados da página do TikTok.',
    'video_not_found' => 'Vídeo não encontrado ou indisponível.',
    'video_private' => 'Este vídeo é privado ou indisponível.',
    'tiktok_extract_info_failed' => 'Não foi possível extrair as informações do vídeo desta página.',
    'instagram_invalid_url'          => 'Não foi possível extrair o ID do post da URL do Instagram.',
    'instagram_extract_failed'       => 'Não foi possível extrair o conteúdo deste post do Instagram. O post pode ser privado ou o Instagram pode estar bloqueando a solicitação.',
    'instagram_story_no_session'     => 'Stories e highlights do Instagram exigem autenticação. Adicione seu INSTAGRAM_SESSION_ID no arquivo .env para habilitar esta função.',
    'instagram_story_requires_auth'  => 'Não foi possível baixar esta story ou highlight. O cookie de sessão pode ter expirado — atualize o INSTAGRAM_SESSION_ID no .env.',
    'reddit_fetch_failed'            => 'Não foi possível buscar o post do Reddit. O post pode ter sido excluído ou ser privado.',
    'reddit_no_media'                => 'Nenhuma mídia para download encontrada neste post do Reddit. Pode ser um post de texto, um link externo ou um formato não suportado.',
    'platform_coming_soon'           => 'O suporte a :platform chega em breve. Fique atento!',
    'unsupported_platform'           => 'Plataforma não suportada. Atualmente suportado: X / Twitter, TikTok, Instagram, Reddit. YouTube Shorts em breve.',
];
