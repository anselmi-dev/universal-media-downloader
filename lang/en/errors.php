<?php

return [
    'twitter_invalid_url' => 'Could not extract a tweet ID from the provided URL.',
    'twitter_fetch_failed' => 'Could not fetch the tweet. Please check the URL and try again.',
    'post_deleted' => 'This post was deleted or is no longer available.',
    'tweet_not_found' => 'Tweet not found. It may have been deleted or the URL is incorrect.',
    'tiktok_invalid_url' => 'Could not extract a video ID from the TikTok URL.',
    'tiktok_page_failed' => 'Could not load the TikTok page. The video may be private or unavailable.',
    'tiktok_extract_failed' => 'Could not extract video data. TikTok may have changed their page structure. Try again later.',
    'tiktok_parse_failed' => 'Could not parse TikTok page data.',
    'video_not_found' => 'Video not found or unavailable.',
    'video_private' => 'This video is private or unavailable.',
    'tiktok_extract_info_failed' => 'Could not extract video information from this page.',
    'instagram_invalid_url'          => 'Could not extract a post ID from the Instagram URL.',
    'instagram_extract_failed'       => 'Could not extract media from this Instagram post. The post may be private or Instagram may be blocking the request. Install yt-dlp for better support.',
    'instagram_story_no_session'     => 'Instagram Stories and Highlights require authentication. Add your INSTAGRAM_SESSION_ID to the .env file to enable this feature.',
    'instagram_story_requires_auth'  => 'Could not download this Story or Highlight. The session cookie may have expired — refresh INSTAGRAM_SESSION_ID in .env.',
    'reddit_fetch_failed'            => 'Could not fetch the Reddit post. The post may be deleted or private.',
    'reddit_no_media'                => 'No downloadable media found in this Reddit post. It may be a text post, a link to an external site, or an unsupported format.',
    'platform_coming_soon'           => ':platform support is coming soon. Stay tuned!',
    'unsupported_platform'           => 'Unsupported platform. Currently supported: X / Twitter, TikTok, Instagram, Reddit. YouTube Shorts is coming soon.',
];
