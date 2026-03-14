<div class="space-y-12">

    {{-- Hero --}}
    <div class="space-y-5">
        <div class="text-xs text-neutral-500 uppercase tracking-widest">◇ {{ __('media downloader') }}</div>
        <h1 class="text-3xl sm:text-4xl font-bold text-white leading-tight tracking-tight">
            {{ __('Download media from social posts') }}
        </h1>
        <p class="text-neutral-400 text-sm leading-relaxed max-w-lg">
            {{ __('Paste any post URL to extract and download every photo and video it contains. No sign-up. No limits.') }}
        </p>

        {{-- Supported platforms chips --}}
        <div class="flex flex-wrap gap-2 pt-1">
            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 border border-neutral-700 text-neutral-300 rounded">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.23H2.747l7.73-8.835L1.254 2.25H8.08l4.261 5.636zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                X / Twitter
            </span>
            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 border border-neutral-700 text-neutral-300 rounded">TikTok</span>
            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 border border-neutral-700 text-neutral-300 rounded">{{ __('Instagram — soon') }}</span>
            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 border border-neutral-700 text-neutral-300 rounded">{{ __('Reddit — soon') }}</span>
        </div>
    </div>

    {{-- Input panel --}}
    <div class="border border-neutral-800 rounded-lg overflow-hidden bg-[#111111]">
        {{-- Panel header --}}
        <div class="flex items-center gap-2 px-4 py-2.5 border-b border-neutral-800 bg-[#0f0f0f]">
            <div class="flex gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-neutral-700"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-neutral-700"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-neutral-700"></span>
            </div>
            <span class="text-xs text-neutral-600 ml-1">{{ __('paste a post url') }}</span>
        </div>

        {{-- Form --}}
        <form wire:submit="download" class="p-4 sm:p-5 flex flex-col sm:flex-row gap-3">
            <div
                x-data="{
                    pasted: false,
                    denied: false,
                    async paste() {
                        this.denied = false
                        try {
                            const text = await navigator.clipboard.readText()
                            const input = $el.querySelector('input')
                            input.value = text
                            input.dispatchEvent(new Event('input', { bubbles: true }))
                            input.focus()
                            this.pasted = true
                            setTimeout(() => this.pasted = false, 2000)
                        } catch {
                            this.denied = true
                            setTimeout(() => this.denied = false, 2500)
                        }
                    }
                }"
                class="flex-1 relative"
            >
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-600 text-sm select-none pointer-events-none">›</span>
                <input
                    wire:model="url"
                    type="url"
                    placeholder="https://x.com/username/status/..."
                    class="w-full h-11 pl-8 pr-28 bg-[#0a0a0a] border border-neutral-700 rounded text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-neutral-400 transition-colors font-[inherit]"
                    autocomplete="off"
                    autofocus
                >
                {{-- Paste button inside the input --}}
                <button
                    type="button"
                    @click="paste()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 h-7 px-2.5 rounded text-[11px] font-bold transition-all"
                    :class="{
                        'bg-neutral-700 text-neutral-300 hover:bg-neutral-600': !pasted && !denied,
                        'bg-green-900/60 text-green-400 border border-green-800': pasted,
                        'bg-red-900/60 text-red-400 border border-red-800': denied
                    }"
                >
                    <span x-show="!pasted && !denied" class="flex items-center gap-1">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="2" width="6" height="4" rx="1"/>
                            <path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2"/>
                        </svg>
                        {{ __('paste') }}
                    </span>
                    <span x-show="pasted" x-cloak>✓ {{ __('pasted') }}</span>
                    <span x-show="denied" x-cloak>✕ {{ __('denied') }}</span>
                </button>
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="h-11 px-6 rounded bg-white text-black text-sm font-bold hover:bg-neutral-200 active:bg-neutral-300 transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 shrink-0 sm:w-auto w-full"
            >
                <span wire:loading.remove wire:target="download">{{ __('download') }}</span>
                <span wire:loading wire:target="download" class="flex items-center gap-2">
                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    {{ __('fetching...') }}
                </span>
            </button>
        </form>

        {{-- Inline validation error --}}
        @error('url')
            <div class="px-5 pb-4 text-xs text-red-400 flex items-center gap-2">
                <span class="text-red-500">✕</span> {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Error state --}}
    @if ($error)
        <div
            x-data
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="border border-red-900/60 rounded-lg bg-red-950/30 p-4 flex gap-3"
        >
            <span class="text-red-500 shrink-0 text-sm">✕</span>
            <div class="space-y-0.5">
                <p class="text-sm text-red-300 font-bold">{{ __('error') }}</p>
                <p class="text-xs text-red-400/80 leading-relaxed">{{ $error }}</p>
            </div>
        </div>
    @endif

    {{-- Results --}}
    @if (!empty($mediaItems))
        <div
            x-data
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-4"
        >
            {{-- Results panel header --}}
            <div class="flex items-center justify-between">
                <div class="text-xs text-neutral-500 uppercase tracking-widest">
                    ◇ {{ __('results') }}
                </div>
                <div class="flex items-center gap-3 text-xs text-neutral-500">
                    @if ($platform)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.23H2.747l7.73-8.835L1.254 2.25H8.08l4.261 5.636zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                            {{ $platform }}
                        </span>
                        <span class="text-neutral-700">·</span>
                    @endif
                    <span>{{ count($mediaItems) }} {{ count($mediaItems) === 1 ? __('item') : __('items') }}</span>
                </div>
            </div>

            {{-- Media grid --}}
            <div class="{{ count($mediaItems) === 1 ? 'max-w-xs' : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3' }}">
                @foreach ($mediaItems as $index => $item)
                    <div class="border border-neutral-800 rounded-lg overflow-hidden bg-[#111111]">

                        {{-- Thumbnail --}}
                        @if ($item['type'] === 'video')
                            <div class="relative bg-neutral-900 aspect-video">
                                @if ($item['thumbnailUrl'])
                                    <img
                                        src="{{ $item['thumbnailUrl'] }}"
                                        alt="Video thumbnail"
                                        class="w-full h-full object-cover opacity-60"
                                        loading="lazy"
                                    >
                                @endif
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-12 h-12 rounded-full border border-neutral-500 bg-black/70 flex items-center justify-center backdrop-blur-sm">
                                        <svg class="w-5 h-5 text-white ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                @if ($item['quality'])
                                    <span class="absolute top-2.5 right-2.5 text-[10px] px-1.5 py-0.5 bg-black/80 text-neutral-400 border border-neutral-700 rounded font-mono">
                                        {{ $item['quality'] }}
                                    </span>
                                @endif
                            </div>
                        @elseif ($item['type'] === 'audio')
                            <div class="relative bg-neutral-900 aspect-video">
                                @if ($item['thumbnailUrl'])
                                    <img
                                        src="{{ $item['thumbnailUrl'] }}"
                                        alt="Audio cover"
                                        class="w-full h-full object-cover opacity-60"
                                        loading="lazy"
                                    >
                                @endif
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-12 h-12 rounded-full border border-neutral-500 bg-black/70 flex items-center justify-center backdrop-blur-sm">
                                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                                        </svg>
                                    </div>
                                </div>
                                @if ($item['quality'])
                                    <span class="absolute top-2.5 right-2.5 text-[10px] px-1.5 py-0.5 bg-black/80 text-neutral-400 border border-neutral-700 rounded font-mono">
                                        {{ $item['quality'] }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="aspect-video bg-neutral-900 overflow-hidden">
                                <img
                                    src="{{ $item['thumbnailUrl'] ?? $item['url'] }}"
                                    alt="Image {{ $index + 1 }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                            </div>
                        @endif

                        {{-- File meta + download --}}
                        <div class="p-3 flex items-center justify-between gap-3 border-t border-neutral-800">
                            <span class="text-xs text-neutral-500">
                                @if ($item['type'] === 'video')
                                    ▶ {{ __('video') }}
                                @elseif ($item['type'] === 'audio')
                                    🎵 {{ __('audio') }}
                                @else
                                    ◻ {{ __('image') }}
                                @endif
                                &nbsp;·&nbsp; #{{ $index + 1 }}
                            </span>
                            <a
                                href="{{ route('download.proxy', ['url' => $item['url'], 'filename' => $item['filename'] ?? 'media-'.($index + 1)]) }}"
                                class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 bg-white text-black font-bold rounded hover:bg-neutral-200 active:bg-neutral-300 transition-colors"
                            >
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                {{ __('download') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif ($hasSearched && !$error)
        <div class="border border-neutral-800 rounded-lg p-8 text-center">
            <p class="text-xs text-neutral-600">◇ {{ __('no media found in this post') }}</p>
        </div>
    @endif

</div>
