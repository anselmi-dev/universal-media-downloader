@php
    $locale = app()->getLocale();
    $supported = array_keys(config('laravellocalization.supportedLocales', ['en' => []]));
    $homeUrl = in_array($locale, $supported, true) ? url("/{$locale}") : url('/');
    $siteName = config('site.name', config('app.name', 'MediaGet'));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') — {{ $siteName }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg?v=2026" type="image/svg+xml">
    <meta name="theme-color" content="#F9F6F1">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|space-mono:400,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#F9F6F1] text-[#2E203B] flex flex-col">

    <header class="border-b border-zinc-200/80 bg-[#F9F6F1]/95 backdrop-blur-sm">
        <div class="max-w-3xl mx-auto px-5 sm:px-0 h-14 flex items-center justify-between">
            <a href="{{ $homeUrl }}" class="flex items-center gap-2 text-sm font-bold tracking-tight text-[#2E203B] hover:opacity-80 transition-opacity">
                @include('partials.icons.platform-icon')
                {{ $siteName }}
            </a>
            <a href="{{ $homeUrl }}" class="text-xs text-[#646464] uppercase tracking-widest hover:text-[#2E203B] transition-colors">
                {{ __('Home') }}
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-5 sm:px-8 py-16 sm:py-24">
        <div class="w-full max-w-3xl">
            <div class="border border-zinc-200 rounded-2xl overflow-hidden bg-white shadow-sm">
                <div class="flex items-center gap-2 px-4 py-2.5 border-b border-zinc-100 bg-[#F2EEE6]">
                    <div class="flex gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#BB89E2]/60"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#F9B646]/60"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-zinc-400"></span>
                    </div>
                    <span class="text-xs text-[#646464] ml-1">{{ __('error') }}</span>
                </div>

                <div class="p-6 sm:p-10 space-y-5">
                    <div class="text-xs text-[#646464] uppercase tracking-widest">◇ {{ __('http_error') }}</div>

                    <div class="flex flex-col sm:flex-row sm:items-end gap-2 sm:gap-4">
                        <p class="font-mono text-5xl sm:text-6xl font-bold text-[#BB89E2] leading-none tracking-tight">
                            @yield('code')
                        </p>
                        <h1 class="text-2xl sm:text-3xl font-bold text-[#2E203B] leading-tight tracking-tight pb-1">
                            @yield('heading')
                        </h1>
                    </div>

                    <p class="text-[#646464] text-sm leading-relaxed max-w-lg">
                        @yield('message')
                    </p>

                    <div class="pt-2">
                        <a
                            href="{{ $homeUrl }}"
                            class="inline-flex items-center gap-2 h-11 px-5 bg-[#2E203B] text-white text-sm font-medium rounded-xl hover:bg-[#3d2c4f] transition-colors"
                        >
                            {{ __('Back to home') }}
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="border-t border-zinc-200/80 py-6 bg-[#F9F6F1]">
        <div class="max-w-3xl mx-auto px-5 sm:px-0 flex items-center justify-between">
            <span class="text-xs text-[#646464]">
                © {{ date('Y') }} {{ config('app.name', 'mediaget') }}
            </span>
            <span class="text-xs text-[#646464]">
                x/twitter &nbsp;·&nbsp; tiktok &nbsp;·&nbsp; {{ __('more coming soon') }}
            </span>
        </div>
    </footer>

</body>
</html>
