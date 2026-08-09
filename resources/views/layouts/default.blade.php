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
        $currentUrl  = url()->current();
        $ogLocales   = [
            'en' => 'en_US',
            'es' => 'es_ES',
            'fr' => 'fr_FR',
            'de' => 'de_DE',
            'pt' => 'pt_BR',
        ];
        $ogLocale = $ogLocales[$locale] ?? 'en_US';

        $platformsCfg = config('site.platforms');
        $social = ($platformsCfg && count($platformsCfg) === 1) ? strtolower($platformsCfg[0]) : 'default';
        $t = fn (string $key) => __("$social.$key") !== "$social.$key" ? __("$social.$key") : __($key);
    @endphp

    {{-- Primary SEO --}}
    <title>{{ $siteTitle }}</title>
    <meta name="description" content="{{ $siteDesc }}">
    @production
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endproduction

    <link rel="canonical" href="{{ $currentUrl }}">

    @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $code => $locMeta)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($code) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getDefaultLocale()) }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $siteTitle }}">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:locale" content="{{ $ogLocale }}">
    @foreach ($ogLocales as $code => $ogAlt)
        @if ($code !== $locale)
            <meta property="og:locale:alternate" content="{{ $ogAlt }}">
        @endif
    @endforeach
    <meta property="og:image" content="{{ asset('og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $siteName }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $siteTitle }}">
    <meta name="twitter:description" content="{{ $siteDesc }}">
    <meta name="twitter:image" content="{{ asset('og-image.png') }}">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg?v=2026" type="image/svg+xml">
    <meta name="theme-color" content="#F9F6F1">

    {{-- JSON-LD --}}
    @php
        $appUrl = rtrim(config('app.url'), '/');
        $homeUrl = url('/'.$locale);

        $schemaWebSite = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'alternateName' => ['Anselmi Downloader', 'AnselmiDev Downloader'],
            'url' => $appUrl.'/',
        ];

        $schemaWebApp = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => $siteName,
            'url' => $currentUrl,
            'description' => $siteDesc,
            'applicationCategory' => 'MultimediaApplication',
            'operatingSystem' => 'Any',
            'browserRequirements' => 'Requires JavaScript',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
            ],
            'featureList' => [
                $t('feature_1_title'),
                $t('feature_2_title'),
                $t('feature_3_title'),
            ],
        ];

        $faqEntity = collect(range(1, 8))
            ->map(function (int $i) use ($t, $social) {
                $q = $t("faq_q{$i}");
                if ($q === "faq_q{$i}" || $q === "{$social}.faq_q{$i}") {
                    return null;
                }

                return [
                    '@type' => 'Question',
                    'name' => $q,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $t("faq_a{$i}"),
                    ],
                ];
            })
            ->filter()
            ->values()
            ->all();

        $schemaFaq = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqEntity,
        ];

        $schemaHowTo = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $t('howto_heading'),
            'description' => $t('intro'),
            'step' => collect([1, 2, 3])->map(fn (int $i) => [
                '@type' => 'HowToStep',
                'position' => $i,
                'name' => $t("howto_{$i}_title"),
                'text' => $t("howto_{$i}_desc"),
            ])->all(),
        ];

        $platformSlug = request()->route('platformSlug');
        $schemaBreadcrumb = null;
        if ($platformSlug) {
            $schemaBreadcrumb = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => __('Home'),
                        'item' => $homeUrl,
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $siteTitle,
                        'item' => $currentUrl,
                    ],
                ],
            ];
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($schemaWebSite, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($schemaWebApp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($schemaFaq, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($schemaHowTo, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @if ($schemaBreadcrumb)
        <script type="application/ld+json">{!! json_encode($schemaBreadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

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
