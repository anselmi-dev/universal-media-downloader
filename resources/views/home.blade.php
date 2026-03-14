@extends('layouts.default')

@section('content')
    @livewire('media-downloader')

    {{-- Features --}}
    <section aria-labelledby="features-heading">
        <h2 id="features-heading" class="text-xs text-neutral-500 uppercase tracking-widest mb-6">◇ {{ __('features_heading') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="border border-neutral-800 rounded-lg p-4 space-y-1.5">
                <p class="text-sm font-bold text-white">{{ __('feature_1_title') }}</p>
                <p class="text-xs text-neutral-500 leading-relaxed">{{ __('feature_1_desc') }}</p>
            </div>
            <div class="border border-neutral-800 rounded-lg p-4 space-y-1.5">
                <p class="text-sm font-bold text-white">{{ __('feature_2_title') }}</p>
                <p class="text-xs text-neutral-500 leading-relaxed">{{ __('feature_2_desc') }}</p>
            </div>
            <div class="border border-neutral-800 rounded-lg p-4 space-y-1.5">
                <p class="text-sm font-bold text-white">{{ __('feature_3_title') }}</p>
                <p class="text-xs text-neutral-500 leading-relaxed">{{ __('feature_3_desc') }}</p>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section aria-labelledby="faq-heading">
        <h2 id="faq-heading" class="text-xs text-neutral-500 uppercase tracking-widest mb-6">◇ {{ __('faq_heading') }}</h2>
        <div class="space-y-px">
            @foreach ([
                ['q' => __('faq_q1'), 'a' => __('faq_a1')],
                ['q' => __('faq_q2'), 'a' => __('faq_a2')],
                ['q' => __('faq_q3'), 'a' => __('faq_a3')],
                ['q' => __('faq_q4'), 'a' => __('faq_a4')],
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
