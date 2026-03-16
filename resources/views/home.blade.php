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
        <h2 id="features-heading" class="text-xs text-[#646464] uppercase tracking-widest mb-6">◇ {{ $t('features_heading') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl p-5 space-y-1.5 bg-[#F2EAF6] shadow-sm">
                <p class="text-sm font-bold text-[#2E203B]">{{ $t('feature_1_title') }}</p>
                <p class="text-xs text-[#646464] leading-relaxed">{{ $t('feature_1_desc') }}</p>
            </div>
            <div class="rounded-2xl p-5 space-y-1.5 bg-[#F2EEE6] shadow-sm">
                <p class="text-sm font-bold text-[#2E203B]">{{ $t('feature_2_title') }}</p>
                <p class="text-xs text-[#646464] leading-relaxed">{{ $t('feature_2_desc') }}</p>
            </div>
            <div class="rounded-2xl p-5 space-y-1.5 bg-[#F2EAF6] shadow-sm">
                <p class="text-sm font-bold text-[#2E203B]">{{ $t('feature_3_title') }}</p>
                <p class="text-xs text-[#646464] leading-relaxed">{{ $t('feature_3_desc') }}</p>
            </div>
        </div>
    </section>

    {{-- Enlaces internos por plataforma (Google: páginas importantes alcanzables por enlaces) --}}
    <section aria-labelledby="platforms-heading">
        <h2 id="platforms-heading" class="text-xs text-[#646464] uppercase tracking-widest mb-6">◇ {{ __('Download by platform') }}</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('platform.show', [app()->getLocale(), 'x-twitter-video-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 bg-[#F0F0F0] text-[#2E203B] hover:bg-[#BB89E2]/20 hover:text-[#2E203B] rounded-xl transition-colors">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.23H2.747l7.73-8.835L1.254 2.25H8.08l4.261 5.636zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                X / Twitter
            </a>
            <a href="{{ route('platform.show', [app()->getLocale(), 'tiktok-video-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 bg-[#F0F0F0] text-[#2E203B] hover:bg-[#BB89E2]/20 hover:text-[#2E203B] rounded-xl transition-colors">TikTok</a>
            <a href="{{ route('platform.show', [app()->getLocale(), 'instagram-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 bg-[#F0F0F0] text-[#2E203B] hover:bg-[#BB89E2]/20 hover:text-[#2E203B] rounded-xl transition-colors">Instagram</a>
            <a href="{{ route('platform.show', [app()->getLocale(), 'reddit-video-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 bg-[#F0F0F0] text-[#2E203B] hover:bg-[#BB89E2]/20 hover:text-[#2E203B] rounded-xl transition-colors">Reddit</a>
        </div>
    </section>

    {{-- FAQ --}}
    <section aria-labelledby="faq-heading">
        <h2 id="faq-heading" class="text-xs text-[#646464] uppercase tracking-widest mb-6">◇ {{ $t('faq_heading') }}</h2>
        <div class="space-y-2">
            @foreach ([
                ['q' => $t('faq_q1'), 'a' => $t('faq_a1')],
                ['q' => $t('faq_q2'), 'a' => $t('faq_a2')],
                ['q' => $t('faq_q3'), 'a' => $t('faq_a3')],
                ['q' => $t('faq_q4'), 'a' => $t('faq_a4')],
            ] as $faq)
                <details class="group border border-zinc-200 rounded-2xl overflow-hidden bg-white shadow-sm">
                    <summary class="flex items-center justify-between px-4 py-3.5 cursor-pointer text-sm text-[#646464] hover:text-[#2E203B] transition-colors list-none select-none">
                        <span class="font-bold text-[#2E203B]">{{ $faq['q'] }}</span>
                        <svg class="w-3.5 h-3.5 shrink-0 ml-4 text-[#646464] transition-transform group-open:rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </summary>
                    <p class="px-4 pb-4 text-xs text-[#646464] leading-relaxed border-t border-zinc-100 pt-3">{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>
    </section>
@endsection
