@extends('layouts.public')

@section('content')
@php
    $__sb = \App\Http\View\Composers\PublicLayoutComposer::sidebarData($cat->id);
    extract($__sb);
    $is_politics = ($cat->slug === 'politics');
    $pills = array_values(array_filter(array_map('trim', explode(',', (string) $cat->pills))));
@endphp

<div class="flex flex-col lg:flex-row gap-8 items-start">
<div class="lg:col-span-8 lg:w-2/3 flex flex-col gap-6">

{{-- Section head --}}
<div class="flex items-center gap-3">
<span class="w-2.5 h-8 {{ $cat->accent_color ?: 'bg-primary' }} rounded-full inline-block"></span>
<div>
<h1 class="font-headline-hero text-headline-hero text-ink-primary tracking-tight">{{ $cat->name_hi }}</h1>
<p class="font-body-sm text-body-sm text-ink-secondary mt-1">{{ $cat->description ?: 'ताज़ा समाचार एवं विश्लेषण' }}</p>
</div>
</div>

{{-- Sub-category pills --}}
@if(!empty($pills))
<div class="flex flex-wrap gap-2">
@foreach($pills as $pill)
<span class="px-3 py-1.5 rounded-full bg-surface-container-low border border-border-hairline font-caption text-caption text-ink-primary hover:text-primary hover:border-primary transition-colors cursor-pointer">{{ $pill }}</span>
@endforeach
</div>
@endif

{{-- Lead package --}}
@if($lead)
<article class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer" onclick="location.href='{{ url('article/'.$lead->slug) }}'">
@if(!empty($lead->image))
<div class="relative w-full aspect-video overflow-hidden">
<img class="w-full h-full object-cover" alt="{{ $lead->image_alt ?: $lead->title }}" src="{{ $lead->image_url }}" loading="lazy"/>
<div class="absolute inset-0 bg-gradient-to-t from-ink-primary/80 via-transparent to-transparent"></div>
<div class="absolute top-4 left-4"><span class="{{ $lead->badge_color ?: 'bg-primary' }} text-on-primary font-label-badge text-label-badge uppercase tracking-wider px-2.5 py-1 rounded font-semibold shadow">{{ $lead->badge_label ?: $lead->cat_name }}</span></div>
</div>
@endif
<div class="p-6">
<h2 class="font-headline-lg text-headline-lg text-ink-primary hover:text-primary transition-colors leading-tight">{{ $lead->title }}</h2>
<p class="font-body-lg text-body-lg text-ink-secondary mt-3 leading-relaxed">{{ $lead->excerpt ?: \App\Support\Site::excerpt($lead->body, 200) }}</p>
<div class="pt-3 flex items-center gap-2 text-xs font-caption text-ink-muted">
<span class="text-primary font-medium">{{ $lead->author_name }}</span><span>•</span><span>{{ $lead->time_display }}</span>
</div>
</div>
</article>
@endif

{{-- Key points strip on lead --}}
@if($lead && $lead->keyPoints->isNotEmpty())
<div class="bg-surface-cream rounded-xl p-5 shadow-sm">
<h3 class="font-headline-sm text-headline-sm font-bold text-ink-primary mb-3">मुख्य बिंदु</h3>
<ul class="space-y-2">
@foreach($lead->keyPoints as $kp)
<li class="flex gap-2 font-body-sm text-body-sm text-ink-secondary leading-relaxed"><span class="text-primary mt-1">◆</span><span>{{ $kp->point }}</span></li>
@endforeach
</ul>
</div>
@endif

{{-- 2×2 grid --}}
@if($grid->isNotEmpty())
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
@foreach($grid as $d)
<article class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer group" onclick="location.href='{{ url('article/'.$d->slug) }}'">
@if(!empty($d->image))
<div class="w-full aspect-[16/9] overflow-hidden"><img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $d->image_alt ?: $d->title }}" src="{{ $d->image_url }}" loading="lazy"/></div>
@endif
<div class="p-4">
<div class="flex items-center gap-2 font-caption text-xs text-ink-muted mb-1.5"><span class="text-primary font-bold">{{ $d->badge_label ?: $d->cat_name }}</span><span>•</span><span>{{ $d->time_display }}</span></div>
<h3 class="font-headline-sm text-headline-sm text-ink-primary group-hover:text-primary transition-colors leading-snug">{{ $d->title }}</h3>
<p class="font-body-sm text-body-sm text-ink-secondary mt-1.5 line-clamp-2">{{ $d->excerpt ?: \App\Support\Site::excerpt($d->body, 120) }}</p>
</div>
</article>
@endforeach
</div>
@endif

{{-- Dossier feed --}}
@if($dossier->isNotEmpty())
<div class="flex flex-col gap-3">
<h3 class="serif text-lg font-bold text-ink-primary">डॉसियर · Dossier</h3>
@foreach($dossier as $d)
<article class="bg-surface-container-lowest p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row gap-4 items-center cursor-pointer" onclick="location.href='{{ url('article/'.$d->slug) }}'">
@if(!empty($d->image))
<div class="w-full sm:w-44 aspect-[4/3] rounded overflow-hidden shrink-0"><img class="w-full h-full object-cover" alt="{{ $d->image_alt ?: $d->title }}" src="{{ $d->image_url }}" loading="lazy"/></div>
@endif
<div class="flex-1 min-w-0">
<div class="flex items-center gap-2 font-caption text-xs text-ink-muted mb-1"><span class="text-primary font-bold">{{ $d->badge_label ?: $d->cat_name }}</span><span>•</span><span>{{ $d->time_display }}</span></div>
<h3 class="font-headline-sm text-headline-sm text-ink-primary hover:text-primary transition-colors leading-snug">{{ $d->title }}</h3>
<p class="font-body-sm text-body-sm text-ink-secondary mt-1.5 line-clamp-2">{{ $d->excerpt ?: \App\Support\Site::excerpt($d->body, 140) }}</p>
</div>
</article>
@endforeach
</div>
@endif

{{-- Opinion strip --}}
@php($opinions = \App\Models\Article::published()->opinion()->where('category_id', $cat->id)->newest()->limit(2)->get())
@if($opinions->isNotEmpty())
<div class="bg-ink-primary rounded-xl p-5 shadow-sm text-inverse-on-surface">
<h3 class="serif text-lg font-bold text-white mb-3">विचार · Opinion</h3>
<div class="space-y-3">
@foreach($opinions as $op)
<a href="{{ url('article/'.$op->slug) }}" class="block group">
<h4 class="font-headline-sm text-sm font-semibold text-white group-hover:text-primary-fixed-dim transition-colors leading-snug">{{ $op->title }}</h4>
<p class="text-xs text-inverse-on-surface mt-1 line-clamp-2">{{ \App\Support\Site::excerpt($op->body, 100) }}</p>
</a>
@endforeach
</div>
</div>
@endif

{{-- Full feed with pagination --}}
<div class="flex flex-col gap-3">
<h3 class="serif text-lg font-bold text-ink-primary">सभी समाचार · All stories</h3>
@forelse($feed as $d)
<article class="bg-surface-container-lowest p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row gap-4 items-center cursor-pointer" onclick="location.href='{{ url('article/'.$d->slug) }}'">
@if(!empty($d->image))
<div class="w-full sm:w-44 aspect-[4/3] rounded overflow-hidden shrink-0"><img class="w-full h-full object-cover" alt="{{ $d->image_alt ?: $d->title }}" src="{{ $d->image_url }}" loading="lazy"/></div>
@endif
<div class="flex-1 min-w-0">
<div class="flex items-center gap-2 font-caption text-xs text-ink-muted mb-1"><span class="text-primary font-bold">{{ $d->badge_label ?: $d->cat_name }}</span><span>•</span><span>{{ $d->time_display }}</span></div>
<h3 class="font-headline-sm text-headline-sm text-ink-primary hover:text-primary transition-colors leading-snug">{{ $d->title }}</h3>
<p class="font-body-sm text-body-sm text-ink-secondary mt-1.5 line-clamp-2">{{ $d->excerpt ?: \App\Support\Site::excerpt($d->body, 140) }}</p>
</div>
</article>
@empty
<div class="bg-surface-container-lowest p-8 rounded-lg text-center text-ink-secondary">इस श्रेणी में अभी कोई समाचार नहीं है।</div>
@endforelse
</div>

{{-- Pagination --}}
@if($feed->hasPages())
<div class="flex items-center justify-between gap-4 p-4 bg-surface-container-lowest rounded-lg shadow-sm">
<div class="font-caption text-caption text-ink-muted">कुल <strong class="text-ink-primary">{{ \App\Support\Site::hiDigits((string) $feed->total()) }}</strong> में से <strong class="text-ink-primary">{{ \App\Support\Site::hiDigits((string) $feed->firstItem()) }}–{{ \App\Support\Site::hiDigits((string) $feed->lastItem()) }}</strong></div>
<div class="flex items-center gap-1">
@if($feed->onFirstPage())
<span class="w-8 h-8 rounded bg-surface-container text-ink-muted flex items-center justify-center opacity-50"><span class="material-symbols-outlined text-[16px]">chevron_left</span></span>
@else
<a href="{{ $feed->previousPageUrl() }}" class="w-8 h-8 rounded bg-surface-container-low hover:bg-surface-container text-ink-primary flex items-center justify-center transition-colors"><span class="material-symbols-outlined text-[16px]">chevron_left</span></a>
@endif
@foreach($feed->getUrlRange(max(1, $feed->currentPage()-2), min($feed->lastPage(), $feed->currentPage()+2)) as $p => $u)
<a href="{{ $u }}" class="w-8 h-8 rounded {{ $p == $feed->currentPage() ? 'bg-primary text-on-primary font-bold shadow-sm' : 'bg-surface-container-low hover:bg-surface-container text-ink-primary font-medium' }} flex items-center justify-center transition-colors">{{ \App\Support\Site::hiDigits((string) $p) }}</a>
@endforeach
@if($feed->hasMorePages())
<a href="{{ $feed->nextPageUrl() }}" class="px-3 h-8 rounded bg-surface-container-low hover:bg-surface-container text-ink-primary flex items-center gap-1 font-semibold transition-colors"><span>अगला पृष्ठ</span><span class="material-symbols-outlined text-[16px]">chevron_right</span></a>
@else
<span class="px-3 h-8 rounded bg-surface-container text-ink-muted flex items-center gap-1 font-semibold opacity-50"><span>अगला पृष्ठ</span><span class="material-symbols-outlined text-[16px]">chevron_right</span></span>
@endif
</div>
</div>
@endif

</div>

@include('partials.sidebar')
</div>
@endsection
