<footer class="border-t border-zinc-200/80 py-6 bg-[#F9F6F1]">
    <div class="max-w-3xl mx-auto px-5 sm:px-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <span class="text-xs text-[#646464]">
            © {{ date('Y') }} {{ config('app.name', 'mediaget') }}
        </span>
        <nav class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-[#646464]" aria-label="{{ __('Download by platform') }}">
            <a href="{{ route('platform.show', [app()->getLocale(), 'x-twitter-video-downloader']) }}" class="hover:text-[#2E203B] transition-colors">X / Twitter</a>
            <span aria-hidden="true">·</span>
            <a href="{{ route('platform.show', [app()->getLocale(), 'tiktok-video-downloader']) }}" class="hover:text-[#2E203B] transition-colors">TikTok</a>
            <span aria-hidden="true">·</span>
            <a href="{{ route('platform.show', [app()->getLocale(), 'instagram-downloader']) }}" class="hover:text-[#2E203B] transition-colors">Instagram</a>
            <span aria-hidden="true">·</span>
            <a href="{{ route('platform.show', [app()->getLocale(), 'reddit-video-downloader']) }}" class="hover:text-[#2E203B] transition-colors">Reddit</a>
        </nav>
    </div>
</footer>
