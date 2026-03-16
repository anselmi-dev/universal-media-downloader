<header class="border-b border-zinc-200/80 bg-[#F9F6F1]/95 backdrop-blur-sm">
    <div class="max-w-3xl mx-auto px-5 sm:px-0 h-14 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm font-bold tracking-tight text-[#2E203B]">
            @include('partials.icons.platform-icon')
            {{ config('site.name', config('app.name', 'MediaGet')) }}
        </div>
        <nav class="flex items-center gap-4 sm:gap-6 text-xs text-[#646464] uppercase tracking-widest">
            @php
                $platformKeyMap = ['Twitter' => 'twitter', 'Instagram' => 'instagram', 'TikTok' => 'tiktok', 'Reddit' => 'reddit', 'YouTube' => 'youtube'];
                $enabledPlatforms = \App\Services\MediaExtractor\MediaExtractorFactory::enabledPlatforms();
                $platformsConfig = config('app.social_media_platforms', []);
            @endphp
            @foreach ($enabledPlatforms as $platform)
                @php
                    $key = $platformKeyMap[$platform] ?? strtolower($platform);
                    $cfg = $platformsConfig[$key] ?? null;
                    $url = $cfg['url'] ?? null;
                @endphp
                @if ($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="hover:text-[#2E203B] transition-colors">{{ $cfg['name'] ?? $platform }}</a>
                @endif
            @endforeach

            <div
                x-data="{ open: false }"
                @click.outside="open = false"
                class="relative"
            >
                <button
                    type="button"
                    @click="open = !open"
                    class="flex items-center gap-1.5 text-xs uppercase tracking-widest text-[#646464] hover:text-[#2E203B] transition-colors"
                >
                    <span>{{ strtoupper(app()->getLocale()) }}</span>
                    <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    x-cloak
                    class="absolute right-0 top-full mt-1.5 py-1 min-w-[8rem] rounded-xl border border-zinc-200 bg-white shadow-lg z-50"
                >
                    @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $code => $locale)
                        <a
                            href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($code) }}"
                            class="block px-3 py-2 text-xs uppercase tracking-widest transition-colors {{ app()->getLocale() === $code ? 'text-[#2E203B] font-bold bg-[#F2EAF6]' : 'text-[#646464] hover:text-[#2E203B] hover:bg-[#F2EEE6]' }}"
                        >
                            {{ $locale['native'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>
    </div>
</header>
