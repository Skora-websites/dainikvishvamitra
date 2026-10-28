@php
$is_politics = false;
$active_nav = 'epaper';
$page_title = 'ई-पेपर आर्काइव | दैनिक विश्वामित्र';
$page_desc = 'दैनिक विश्वामित्र का प्रिंट संस्करण ऑनलाइन पढ़ें एवं PDF रूप में डाउनलोड करें।';

$__sb = App\Http\View\Composers\PublicLayoutComposer::sidebarData(null);
extract($__sb);
$archive = ($archive ?? null);
$sidebar_back = '';
@endphp

@extends('layouts.public')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    <div class="lg:col-span-8 flex flex-col gap-5">
        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-border-hairline p-6">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[32px]">menu_book</span>
                <div>
                    <h1 class="font-headline-md text-xl sm:text-2xl text-ink-primary font-bold">ई-पेपर आर्काइव (E-Paper Archive)</h1>
                    <p class="font-caption text-xs sm:text-sm text-ink-secondary mt-0.5">दैनिक विश्वामित्र का प्रिंट संस्करण ऑनलाइन पढ़ें व PDF डाउनलोड करें।</p>
                </div>
            </div>
        </div>

        @if ($papers->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($papers as $p)
                    <div class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-border-hairline hover:shadow-md transition-all flex gap-4 items-start group">
                        <a href="{{ route('epaper.show', $p->id) }}" class="w-24 h-32 rounded-lg overflow-hidden shrink-0 bg-surface-container flex items-center justify-center border border-border-hairline group-hover:border-primary transition-colors">
                            @if (!empty($p->cover_image))
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform" alt="{{ e($p->title) }}" src="{{ asset(ltrim((string) $p->cover_image, '/')) }}"/>
                            @else
                                <span class="material-symbols-outlined text-ink-muted text-[40px] group-hover:text-primary transition-colors">newspaper</span>
                            @endif
                        </a>
                        <div class="min-w-0 flex-1 flex flex-col justify-between h-full">
                            <div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-primary/10 text-primary mb-1">दैनिक विश्वामित्र</span>
                                <h3 class="font-headline-sm text-sm sm:text-base font-bold text-ink-primary leading-snug group-hover:text-primary transition-colors">
                                    <a href="{{ route('epaper.show', $p->id) }}">{{ e($p->title) }}</a>
                                </h3>
                                <p class="font-caption text-xs text-ink-muted mt-1">
                                    {{ e(\App\Support\Site::hiDateShort($p->edition_date)) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 mt-3 pt-2 border-t border-border-hairline/60">
                                <a href="{{ route('epaper.show', $p->id) }}" class="inline-flex items-center gap-1 bg-primary text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-masthead-crimson transition-colors shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">menu_book</span>
                                    <span>पढ़ें · Read</span>
                                </a>
                                <a href="{{ asset(ltrim((string) $p->pdf_path, '/')) }}" download class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-border-hairline hover:border-primary text-ink-secondary hover:text-primary text-xs font-semibold transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">download</span>
                                    <span>PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-surface-container-lowest p-12 rounded-xl border border-border-hairline text-center">
                <span class="material-symbols-outlined text-ink-muted text-[48px]">menu_book</span>
                <p class="font-headline-sm text-base text-ink-secondary mt-3 font-semibold">ई-पेपर अंक शीघ्र उपलब्ध होंगे।</p>
                <p class="font-caption text-xs text-ink-muted mt-1">कृपया कुछ समय पश्चात पुनः देखें।</p>
            </div>
        @endif
    </div>

    @include('partials.sidebar')
</div>
@endsection
