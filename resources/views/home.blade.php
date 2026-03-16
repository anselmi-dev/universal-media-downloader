@extends('layouts.default')

@section('content')
    @livewire('media-downloader')

    @php
        $platforms = config('site.platforms');
        $social    = ($platforms && count($platforms) === 1) ? strtolower($platforms[0]) : 'default';
        // Try platform-specific key first, fall back to generic
        $t = fn(string $key) => __("$social.$key") !== "$social.$key" ? __("$social.$key") : __($key);
    @endphp

    {{-- Features --}}
    <section aria-labelledby="features-heading">
        <h2 id="features-heading" class="text-xs text-neutral-500 uppercase tracking-widest mb-6">◇ {{ $t('features_heading') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="border border-neutral-800 rounded-lg p-4 space-y-1.5">
                <p class="text-sm font-bold text-white">{{ $t('feature_1_title') }}</p>
                <p class="text-xs text-neutral-500 leading-relaxed">{{ $t('feature_1_desc') }}</p>
            </div>
            <div class="border border-neutral-800 rounded-lg p-4 space-y-1.5">
                <p class="text-sm font-bold text-white">{{ $t('feature_2_title') }}</p>
                <p class="text-xs text-neutral-500 leading-relaxed">{{ $t('feature_2_desc') }}</p>
            </div>
            <div class="border border-neutral-800 rounded-lg p-4 space-y-1.5">
                <p class="text-sm font-bold text-white">{{ $t('feature_3_title') }}</p>
                <p class="text-xs text-neutral-500 leading-relaxed">{{ $t('feature_3_desc') }}</p>
            </div>
        </div>
    </section>

    {{-- Enlaces internos por plataforma (Google: páginas importantes alcanzables por enlaces) --}}
    <section aria-labelledby="platforms-heading">
        <h2 id="platforms-heading" class="text-xs text-neutral-500 uppercase tracking-widest mb-6">◇ {{ __('Download by platform') }}</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('platform.show', [app()->getLocale(), 'x-twitter-video-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 border border-neutral-700 text-neutral-300 hover:text-white hover:border-neutral-600 rounded transition-colors">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.23H2.747l7.73-8.835L1.254 2.25H8.08l4.261 5.636zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                X / Twitter
            </a>
            <a href="{{ route('platform.show', [app()->getLocale(), 'tiktok-video-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 border border-neutral-700 text-neutral-300 hover:text-white hover:border-neutral-600 rounded transition-colors">TikTok</a>
            <a href="{{ route('platform.show', [app()->getLocale(), 'instagram-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 border border-neutral-700 text-neutral-300 hover:text-white hover:border-neutral-600 rounded transition-colors">Instagram</a>
            <a href="{{ route('platform.show', [app()->getLocale(), 'reddit-video-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 border border-neutral-700 text-neutral-300 hover:text-white hover:border-neutral-600 rounded transition-colors">Reddit</a>
        </div>
    </section>

    {{-- FAQ --}}
    <section aria-labelledby="faq-heading">
        <h2 id="faq-heading" class="text-xs text-neutral-500 uppercase tracking-widest mb-6">◇ {{ $t('faq_heading') }}</h2>
        <div class="space-y-px">
            @foreach ([
                ['q' => $t('faq_q1'), 'a' => $t('faq_a1')],
                ['q' => $t('faq_q2'), 'a' => $t('faq_a2')],
                ['q' => $t('faq_q3'), 'a' => $t('faq_a3')],
                ['q' => $t('faq_q4'), 'a' => $t('faq_a4')],
            ] as $faq)
                <details class="group border border-neutral-800 first:rounded-t-lg last:rounded-b-lg -mt-px first:mt-0 bg-[#111111]">
                    <summary class="flex items-center justify-between px-4 py-3.5 cursor-pointer text-sm text-neutral-300 hover:text-white transition-colors list-none select-none">
                        <span class="font-bold">{{ $faq['q'] }}</span>
                        <svg class="w-3.5 h-3.5 shrink-0 ml-4 text-neutral-600 transition-transform group-open:rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </summary>
                    <p class="px-4 pb-4 text-xs text-neutral-500 leading-relaxed border-t border-neutral-800 pt-3">{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>
    </section>
@endsection
