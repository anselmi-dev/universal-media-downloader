<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $locale      = app()->getLocale();
        $siteTitle   = config("site.seo.$locale.title")   ?? config('site.seo.en.title')   ?? __('meta_title');
        $siteDesc    = config("site.seo.$locale.description") ?? config('site.seo.en.description') ?? __('meta_description');
        $siteName    = config('site.name', config('app.name', 'MediaGet'));
    @endphp

    {{-- Primary SEO --}}
    <title>{{ $siteTitle }}</title>
    <meta name="description" content="{{ $siteDesc }}">
    @production
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endproduction

    {{-- Canonical: versión representativa de la URL (Google recomienda rel="canonical") --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- hreflang: URLs reales por idioma (mcamara/laravel-localization) --}}
    @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $code => $locale)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($code) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getDefaultLocale()) }}">


    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $siteTitle }}">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:locale" content="{{ match(app()->getLocale()) { 'es' => 'es_ES', 'fr' => 'fr_FR', 'de' => 'de_DE', 'pt' => 'pt_BR', default => 'en_US' } }}">
    <meta property="og:image" content="{{ asset('og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $siteName }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $siteTitle }}">
    <meta name="twitter:description" content="{{ $siteDesc }}">
    <meta name="twitter:image" content="{{ asset('og-image.png') }}">

    {{-- Favicons (Google recomienda link rel="icon" para elegibilidad del favicon en búsqueda) --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <meta name="theme-color" content="#F9F6F1">

    {{-- JSON-LD --}}
    @php
        $appUrl = rtrim(config('app.url'), '/');
        $baseUrl = $appUrl . '/';

        // WebSite: señal principal para el site name (Google recomienda WebSite structured data)
        $schemaWebSite = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'           => $siteName,
            'alternateName'  => ['Anselmi Downloader', 'AnselmiDev Downloader'],
            'url'            => $baseUrl,
        ];

        $schemaWebApp = [
            '@context'            => 'https://schema.org',
            '@type'               => 'WebApplication',
            'name'                => $siteName,
            'url'                 => $appUrl . '/',
            'description'         => $siteDesc,
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
    <script type="application/ld+json">{!! json_encode($schemaWebSite, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($schemaWebApp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($schemaFaq, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|space-mono:400,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#F9F6F1] text-[#2E203B] flex flex-col">

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
