<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Primary SEO --}}
    <title>{{ config('app.name', 'MediaGet') }} — {{ __('meta_title') }}</title>
    <meta name="description" content="{{ __('meta_description') }}">
    @production
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endproduction

    <link rel="canonical" href="{{ url('/') }}">

    {{-- Language alternates --}}
    <link rel="alternate" hreflang="en" href="{{ url('/') }}">
    <link rel="alternate" hreflang="es" href="{{ url('/') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">


    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="{{ config('app.name', 'MediaGet') }}">
    <meta property="og:title" content="{{ config('app.name', 'MediaGet') }} — {{ __('meta_title') }}">
    <meta property="og:description" content="{{ __('meta_description') }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'es' ? 'es_ES' : 'en_US' }}">
    <meta property="og:image" content="{{ asset('og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ config('app.name', 'MediaGet') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name', 'MediaGet') }} — {{ __('meta_title') }}">
    <meta name="twitter:description" content="{{ __('meta_description') }}">
    <meta name="twitter:image" content="{{ asset('og-image.png') }}">

    {{-- Favicons --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    {{-- <link rel="icon" href="/favicon.svg" type="image/svg+xml"> --}}
    {{-- <link rel="apple-touch-icon" href="/apple-touch-icon.png"> --}}
    <meta name="theme-color" content="#0a0a0a">

    {{-- JSON-LD --}}
    @php
        $appUrl   = rtrim(config('app.url'), '/');
        $appName  = config('app.name', 'MediaGet');

        $schemaWebApp = [
            '@context'            => 'https://schema.org',
            '@type'               => 'WebApplication',
            'name'                => $appName,
            'url'                 => $appUrl . '/',
            'description'         => __('meta_description'),
            'applicationCategory' => 'MultimediaApplication',
            'operatingSystem'     => 'Any',
            'browserRequirements' => 'Requires JavaScript',
            'offers' => [
                '@type'         => 'Offer',
                'price'         => '0',
                'priceCurrency' => 'USD',
            ],
            'featureList' => [
                'Download videos from X / Twitter without watermark',
                'Download TikTok videos without watermark',
                'Download photos from social media posts',
                'No registration required',
                'Free unlimited downloads',
            ],
        ];

        $schemaFaq = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => collect(['faq_q1', 'faq_q2', 'faq_q3', 'faq_q4'])
                ->map(fn ($k, $i) => [
                    '@type'          => 'Question',
                    'name'           => __($k),
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => __('faq_a' . ($i + 1))],
                ])
                ->values()
                ->all(),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($schemaWebApp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($schemaFaq, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-mono:400,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Space Mono', ui-monospace, monospace; }
    </style>
</head>
<body class="min-h-screen bg-[#0a0a0a] text-neutral-200 flex flex-col">

    @include('layouts.default.header')

    <main class="flex-1 flex items-start justify-center px-5 sm:px-8 pb-16 pt-8 sm:pb-24 sm:pt-8">
        <div class="w-full max-w-3xl space-y-20">
            @yield('content')
        </div>
    </main>

    @include('layouts.default.footer')

    @livewireScripts
</body>
</html>
