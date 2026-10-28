@php
use App\Models\Video;
use App\Support\Site;

$active_nav = 'video';
$page_title = 'वीडियो | दैनिक विश्वामित्र';

$videos = Video::where('is_active', true)
    ->where(function ($q) {
        $q->whereNull('published_at')->orWhere('published_at', '<=', now());
    })
    ->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('id')
    ->limit(24)->get();

$play = (int) request('play', 0);
$featured = null;
foreach ($videos as $v) {
    if ((int) $v->id === $play) {
        $featured = $v;
        break;
    }
}
if (!$featured && count($videos)) {
    $featured = $videos[0];
}

// Legacy yt_thumb(): explicit thumbnail wins, else the YouTube auto-generated frame.
$yt_thumb = static function ($v) {
    $t = trim((string) $v->thumbnail);
    if ($t !== '') {
        if (preg_match('#^https?://#i', $t) || str_starts_with($t, 'data:')) {
            return $t;
        }
        return asset(ltrim($t, '/'));
    }
    return 'https://i.ytimg.com/vi/' . urlencode((string) $v->youtube_id) . '/hqdefault.jpg';
};

$__sb = App\Http\View\Composers\PublicLayoutComposer::sidebarData(null);
extract($__sb);
$is_politics = ($is_politics ?? false);
$archive = ($archive ?? null);
$sidebar_back = url('video');
@endphp

@extends('layouts.public')

@section('content')
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-8 flex flex-col gap-6">
<div class="flex items-center gap-3">
<span class="w-1.5 h-6 bg-primary rounded"></span>
<div>
<h1 class="font-headline-lg text-headline-lg text-ink-primary">वीडियो गैलरी</h1>
<p class="font-caption text-caption text-ink-secondary">दिनभर की बड़ी खबरें — देखें व सुनें</p>
</div>
</div>
@if ($featured)
<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
<div class="relative w-full aspect-video bg-ink-primary">
<iframe class="absolute inset-0 w-full h-full" src="https://www.youtube-nocookie.com/embed/{{ $featured->youtube_id }}" title="{{ $featured->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
</div>
<div class="p-5">
<h2 class="font-headline-md text-headline-md text-ink-primary">{{ $featured->title }}</h2>
@if (!empty($featured->description))<p class="font-body-md text-body-md text-ink-secondary mt-2">{{ $featured->description }}</p>@endif
<div class="flex items-center gap-3 mt-3 text-xs font-caption text-ink-muted">
@if (!empty($featured->duration_text))<span class="flex items-center gap-1"><span class="material-symbols-outlined text-[15px]">schedule</span>{{ $featured->duration_text }}</span>@endif
@if (!empty($featured->published_at))<span>•</span><span>{{ Site::hiDateShort($featured->published_at) }}</span>@endif
</div>
</div>
</div>
@endif
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
@forelse ($videos as $v)
@php $is_cur = $featured && (int) $v->id === (int) $featured->id; @endphp
<a href="{{ url('video?play=' . (int) $v->id) }}" class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow group @if($is_cur) ring-2 ring-primary @endif">
<div class="relative aspect-video overflow-hidden bg-surface-container-high">
<img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $v->title }}" src="{{ $yt_thumb($v) }}" loading="lazy"/>
<div class="absolute inset-0 flex items-center justify-center"><span class="w-12 h-12 rounded-full bg-primary/90 text-on-primary flex items-center justify-center"><span class="material-symbols-outlined text-[28px]">play_arrow</span></span></div>
@if (!empty($v->duration_text))<span class="absolute bottom-2 right-2 bg-ink-primary/85 text-on-primary text-[11px] px-1.5 py-0.5 rounded font-semibold">{{ $v->duration_text }}</span>@endif
</div>
<div class="p-4">
<h3 class="font-headline-sm text-headline-sm text-ink-primary group-hover:text-primary transition-colors line-clamp-2">{{ $v->title }}</h3>
@if (!empty($v->published_at))<p class="font-caption text-caption text-ink-muted mt-1">{{ Site::hiDateShort($v->published_at) }}</p>@endif
</div>
</a>
@empty
<div class="sm:col-span-2 bg-surface-container-lowest p-10 rounded-xl text-center text-ink-secondary">वीडियो शीघ्र उपलब्ध होंगे।</div>
@endforelse
</div>
</div>
@include('partials.sidebar')
</div>
</div></main>
@endsection
