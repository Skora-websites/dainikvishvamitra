{{--
    Flag hub partial — shared by breaking.blade.php and opinion.blade.php.

    Expected variables:
      $hub_kind    'breaking' | 'opinion'   (mirrors legacy $hub_kind)
      $lead        ?App\Models\Article       lead story (page 1 only)
      $articles    paginated list excluding the lead
      $pagination  ['total','per','page','pages','from','to']

    This partial IS the page shell for both hubs (it sets $page_title/$active_nav
    and @extends the layout), so breaking.blade.php and opinion.blade.php only set
    $hub_kind — mirroring how legacy breaking.php/opinion.php only set $hub_kind
    and then required hub.php.
--}}
@php
use App\Support\Site;

$HUB = [
    'breaking' => [
        'title' => 'ब्रेकिंग न्यूज़',
        'sub' => 'ताज़ा व बड़ी खबरें — सबसे पहले',
        'badge' => 'ब्रेकिंग',
        'accent' => 'bg-breaking-red',
        'nav' => 'breaking',
        'file' => 'breaking',
    ],
    'opinion' => [
        'title' => 'विचार एवं लेख',
        'sub' => 'संपादकीय, स्तंभ व विशेषज्ञ विश्लेषण',
        'badge' => 'विचार',
        'accent' => 'bg-tertiary',
        'nav' => 'editorial-and-opinion',
        'file' => 'opinion',
    ],
][$hub_kind];

$total = $pagination['total'];
$page = $pagination['page'];
$pages = $pagination['pages'];
$per = $pagination['per'];
$from = $pagination['from'];
$to = $pagination['to'];
$base_q = url($HUB['file']) . '?';

// The hub is the page shell for both breaking and opinion — it owns the metadata.
$page_title = $HUB['title'] . ' | दैनिक विश्वामित्र';
$active_nav = $HUB['nav'];

// Legacy breaking.php/opinion.php both required includes/sidebar.php, which pulled
// in $most_read / $cat_poll / $side_bul / $events / $archive itself. Since the hub
// is now the single partial both pages include, it must supply that data.
$__sb = App\Http\View\Composers\PublicLayoutComposer::sidebarData(null);
extract($__sb);
$is_politics = ($is_politics ?? false);
$archive = ($archive ?? null);
$sidebar_back = url($HUB['file'] . ($page > 1 ? '?page=' . $page : ''));
@endphp

@extends('layouts.public')

@section('content')
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-8 flex flex-col gap-6">
<div class="flex items-center gap-3">
<span class="w-2.5 h-8 {{ $HUB['accent'] }} rounded-full inline-block"></span>
<div>
<h1 class="font-headline-hero text-headline-hero text-ink-primary tracking-tight">{{ $HUB['title'] }}</h1>
<p class="font-body-sm text-body-sm text-ink-secondary mt-1">{{ $HUB['sub'] }}</p>
</div>
</div>
@if ($page === 1 && $lead)
<article class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer" onclick="location.href='{{ url('article/' . $lead->slug) }}'">
@if (!empty($lead->image))
<div class="relative w-full aspect-video overflow-hidden">
<img class="w-full h-full object-cover" alt="{{ $lead->image_alt ?: $lead->title }}" src="{{ $lead->image_url }}"/>
<div class="absolute inset-0 bg-gradient-to-t from-ink-primary/80 via-transparent to-transparent"></div>
<div class="absolute top-4 left-4"><span class="{{ $HUB['accent'] }} text-on-primary font-label-badge text-label-badge uppercase tracking-wider px-2.5 py-1 rounded font-semibold shadow">{{ $lead->badge_label ?: $HUB['badge'] }}</span></div>
</div>
@endif
<div class="p-6">
<h2 class="font-headline-lg text-headline-lg text-ink-primary hover:text-primary transition-colors leading-tight">{{ $lead->title }}</h2>
<p class="font-body-lg text-body-lg text-ink-secondary mt-3 leading-relaxed">{{ $lead->excerpt ?: Site::excerpt($lead->body ?? '', 200) }}</p>
<div class="pt-3 flex items-center gap-2 text-xs font-caption text-ink-muted">
<span class="text-primary font-medium">{{ $lead->author_name }}</span><span>•</span><span>{{ $lead->time_display }}</span>
</div>
</div>
</article>
@endif
<div class="flex flex-col gap-3">
@foreach ($articles as $d)
<article class="bg-surface-container-lowest p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row gap-4 items-center cursor-pointer" onclick="location.href='{{ url('article/' . $d->slug) }}'">
@if (!empty($d->image))
<div class="w-full sm:w-44 aspect-[4/3] rounded overflow-hidden shrink-0"><img class="w-full h-full object-cover" alt="{{ $d->image_alt ?: $d->title }}" src="{{ $d->image_url }}" loading="lazy"/></div>
@endif
<div class="flex-1 min-w-0">
<div class="flex items-center gap-2 font-caption text-xs text-ink-muted mb-1"><span class="text-primary font-bold">{{ $d->badge_label ?: optional($d->category)->name_hi }}</span><span>•</span><span>{{ $d->time_display }}</span></div>
<h3 class="font-headline-sm text-headline-sm text-ink-primary hover:text-primary transition-colors leading-snug">{{ $d->title }}</h3>
<p class="font-body-sm text-body-sm text-ink-secondary mt-1.5 line-clamp-2">{{ $d->excerpt ?: Site::excerpt($d->body ?? '', 140) }}</p>
</div>
</article>
@endforeach
@if (!$lead && !count($articles))<div class="bg-surface-container-lowest p-8 rounded-lg text-center text-ink-secondary">इस अनुभाग में अभी कोई समाचार नहीं है।</div>@endif
</div>
<div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-surface-container-lowest rounded-lg shadow-sm">
<div class="font-caption text-caption text-ink-muted">कुल <strong class="text-ink-primary">{{ Site::hiDigits((string) $total) }}</strong> लेखों में से <strong class="text-ink-primary">{{ Site::hiDigits((string) $from) }}–{{ Site::hiDigits((string) $to) }}</strong> प्रदर्शित</div>
<div class="flex items-center gap-1 font-label-badge text-xs">
@if ($page > 1)<a href="{{ $base_q }}page={{ $page - 1 }}" class="w-8 h-8 rounded bg-surface-container-low hover:bg-surface-container text-ink-primary flex items-center justify-center transition-colors"><span class="material-symbols-outlined text-[16px]">chevron_left</span></a>@endif
@for ($p = 1; $p <= $pages; $p++)
@if ($p > 3 && $p < $pages && abs($p - $page) > 1) @continue @endif
<a href="{{ $base_q }}page={{ $p }}" class="w-8 h-8 rounded @if($p === $page) bg-primary text-on-primary font-bold shadow-sm @else bg-surface-container-low hover:bg-surface-container text-ink-primary font-medium @endif flex items-center justify-center transition-colors">{{ Site::hiDigits((string) $p) }}</a>
@endfor
@if ($page < $pages)<a href="{{ $base_q }}page={{ $page + 1 }}" class="px-3 h-8 rounded bg-surface-container-low hover:bg-surface-container text-ink-primary flex items-center gap-1 font-semibold transition-colors"><span>अगला पृष्ठ</span><span class="material-symbols-outlined text-[16px]">chevron_right</span></a>@endif
</div>
</div>
</div>
@include('partials.sidebar')
</div>
</div></main>
@endsection
