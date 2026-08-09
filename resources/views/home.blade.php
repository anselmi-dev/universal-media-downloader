@extends('layouts.default')

@section('content')
    @livewire('media-downloader')

    @php
        $platforms = config('site.platforms');
        $social    = ($platforms && count($platforms) === 1) ? strtolower($platforms[0]) : 'default';
        $t = fn(string $key) => __("$social.$key") !== "$social.$key" ? __("$social.$key") : __($key);
        $faqItems = collect(range(1, 8))
            ->map(function (int $i) use ($t, $social) {
                $q = $t("faq_q{$i}");
                if ($q === "faq_q{$i}" || $q === "{$social}.faq_q{$i}") {
                    return null;
                }

                return ['q' => $q, 'a' => $t("faq_a{$i}")];
            })
            ->filter()
            ->values();
        $locale = app()->getLocale();
    @endphp

    {{-- Intro --}}
    <section class="space-y-3">
        <p class="text-sm text-[#646464] leading-relaxed max-w-2xl">
            {{ $t('intro') }}
        </p>
    </section>

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

    {{-- How to --}}
    <section aria-labelledby="howto-heading">
        <h2 id="howto-heading" class="text-xs text-[#646464] uppercase tracking-widest mb-6">◇ {{ $t('howto_heading') }}</h2>
        <ol class="space-y-3">
            @foreach ([1, 2, 3] as $step)
                <li class="flex gap-4 border border-zinc-200 rounded-2xl bg-white shadow-sm p-4 sm:p-5">
                    <span class="shrink-0 w-8 h-8 rounded-xl bg-[#F2EAF6] text-[#2E203B] text-sm font-bold flex items-center justify-center" aria-hidden="true">{{ $step }}</span>
                    <div class="space-y-1 min-w-0">
                        <p class="text-sm font-bold text-[#2E203B]">{{ $t("howto_{$step}_title") }}</p>
                        <p class="text-xs text-[#646464] leading-relaxed">{{ $t("howto_{$step}_desc") }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Long-form content (word count / sentence length for SEO tools) --}}
    <section aria-labelledby="content-more-heading" class="space-y-4">
        <h2 id="content-more-heading" class="text-xs text-[#646464] uppercase tracking-widest">◇ {{ $t('content_more_heading') }}</h2>
        <div class="space-y-4 text-sm text-[#646464] leading-relaxed max-w-2xl">
            @foreach (preg_split('/\n\s*\n/', trim($t('content_more'))) as $paragraph)
                @if (filled(trim($paragraph)))
                    <p>{{ trim($paragraph) }}</p>
                @endif
            @endforeach
        </div>
    </section>

    {{-- Internal links with unique anchor texts --}}
    <section aria-labelledby="platforms-heading">
        <h2 id="platforms-heading" class="text-xs text-[#646464] uppercase tracking-widest mb-6">◇ {{ __('Download by platform') }}</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('platform.show', [$locale, 'x-twitter-video-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 bg-[#F0F0F0] text-[#2E203B] hover:bg-[#BB89E2]/20 hover:text-[#2E203B] rounded-xl transition-colors">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.23H2.747l7.73-8.835L1.254 2.25H8.08l4.261 5.636zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                {{ __('Download X videos') }}
            </a>
            <a href="{{ route('platform.show', [$locale, 'tiktok-video-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 bg-[#F0F0F0] text-[#2E203B] hover:bg-[#BB89E2]/20 hover:text-[#2E203B] rounded-xl transition-colors">{{ __('Download TikTok videos') }}</a>
            <a href="{{ route('platform.show', [$locale, 'instagram-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 bg-[#F0F0F0] text-[#2E203B] hover:bg-[#BB89E2]/20 hover:text-[#2E203B] rounded-xl transition-colors">{{ __('Download Instagram media') }}</a>
            <a href="{{ route('platform.show', [$locale, 'reddit-video-downloader']) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-2 bg-[#F0F0F0] text-[#2E203B] hover:bg-[#BB89E2]/20 hover:text-[#2E203B] rounded-xl transition-colors">{{ __('Download Reddit videos') }}</a>
        </div>
    </section>

    {{-- FAQ --}}
    <section aria-labelledby="faq-heading">
        <h2 id="faq-heading" class="text-xs text-[#646464] uppercase tracking-widest mb-6">◇ {{ $t('faq_heading') }}</h2>
        <div class="space-y-2">
            @foreach ($faqItems as $faq)
                <details class="group border border-zinc-200 rounded-2xl overflow-hidden bg-white shadow-sm">
                    <summary class="flex items-center justify-between px-4 py-3.5 cursor-pointer text-sm text-[#646464] hover:text-[#2E203B] transition-colors list-none select-none">
                        <span class="font-bold text-[#2E203B]">{{ $faq['q'] }}</span>
                        <svg class="w-3.5 h-3.5 shrink-0 ml-4 text-[#646464] transition-transform group-open:rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </summary>
                    <p class="px-4 pb-4 text-xs text-[#646464] leading-relaxed border-t border-zinc-100 pt-3">{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>
    </section>

    {{-- External links (Seobility) + legal --}}
    <section aria-labelledby="external-links-heading" class="space-y-3 border-t border-zinc-200/80 pt-6">
        <h2 id="external-links-heading" class="text-xs text-[#646464] uppercase tracking-widest">◇ {{ __('Official platform links') }}</h2>
        <p class="text-xs text-[#525252] leading-relaxed max-w-2xl">
            {{ $t('legal_note') }}
            {{ __('Learn more') }}:
            <a href="https://help.x.com/" target="_blank" rel="noopener noreferrer" class="underline hover:text-[#2E203B]">X Help Center</a>,
            <a href="https://support.tiktok.com/" target="_blank" rel="noopener noreferrer" class="underline hover:text-[#2E203B]">TikTok Support</a>,
            <a href="https://help.instagram.com/" target="_blank" rel="noopener noreferrer" class="underline hover:text-[#2E203B]">Instagram Help</a>,
            <a href="https://support.reddithelp.com/" target="_blank" rel="noopener noreferrer" class="underline hover:text-[#2E203B]">Reddit Help</a>.
        </p>
    </section>
@endsection
