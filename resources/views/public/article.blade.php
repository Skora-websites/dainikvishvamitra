@extends('layouts.public')

@section('content')
@php
    $__sb = \App\Http\View\Composers\PublicLayoutComposer::sidebarData($art->category_id);
    extract($__sb);
    $is_politics = ($art->cat_slug === 'politics');
@endphp

<div class="flex flex-col lg:flex-row gap-8 items-start">
<article class="lg:w-2/3 flex flex-col gap-6">

{{-- Breadcrumb --}}
<nav class="font-caption text-caption text-ink-muted flex items-center gap-1.5" aria-label="ब्रेडक्रम्ब">
<a class="hover:text-primary transition-colors" href="{{ url('/') }}">होम</a>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<a class="hover:text-primary transition-colors" href="{{ url('category/'.$art->cat_slug) }}">{{ $art->cat_name }}</a>
</nav>

{{-- Headline block --}}
<header class="flex flex-col gap-3">
@if($art->badge_label)
<span class="{{ $art->badge_color ?: 'bg-primary' }} text-on-primary font-label-badge text-label-badge uppercase tracking-wider px-2.5 py-1 rounded font-semibold self-start">{{ $art->badge_label }}</span>
@endif
<h1 class="font-headline-hero text-headline-hero text-ink-primary tracking-tight leading-tight">{{ $art->title }}</h1>
@if($art->excerpt)
<p class="font-body-lg text-body-lg text-ink-secondary leading-relaxed">{{ $art->excerpt }}</p>
@endif
<div class="flex flex-wrap items-center gap-x-4 gap-y-2 font-caption text-caption text-ink-muted pt-1 border-b border-border-hairline pb-4">
<span class="flex items-center gap-1 text-primary font-semibold"><span class="material-symbols-outlined text-[15px]">edit</span>{{ $art->author_name }}</span>
@if($art->author_role)<span>{{ $art->author_role }}</span>@endif
@if($art->location)<span class="flex items-center gap-1"><span class="material-symbols-outlined text-[15px]">location_on</span>{{ $art->location }}</span>@endif
<span class="flex items-center gap-1"><span class="material-symbols-outlined text-[15px]">schedule</span>{{ $art->time_display }}</span>
<span class="flex items-center gap-1"><span class="material-symbols-outlined text-[15px]">visibility</span>{{ \App\Support\Site::hiDigits(number_format((int) $art->views)) }}</span>
</div>
</header>

{{-- Lead image --}}
@if(!empty($art->image))
<figure class="flex flex-col gap-2">
<img alt="{{ $art->image_alt ?: $art->title }}" class="w-full rounded-lg object-cover" src="{{ $art->image_url }}"/>
@if($art->image_caption)
<figcaption class="font-caption text-caption text-ink-muted">{{ $art->image_caption }}</figcaption>
@endif
</figure>
@endif

{{-- Author box --}}
<div class="bg-surface-cream rounded-xl p-4 shadow-sm flex items-start gap-4">
@if($art->author_image_url)
<img alt="{{ $art->author_name }}" class="w-12 h-12 rounded-full object-cover shrink-0" src="{{ $art->author_image_url }}"/>
@else
<div class="w-12 h-12 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-lg shrink-0">{{ mb_substr((string) $art->author_name, 0, 1) }}</div>
@endif
<div class="min-w-0">
<div class="font-body-sm text-body-sm font-semibold text-ink-primary">
<a class="hover:text-primary transition-colors" href="{{ url('author/'.rawurlencode((string) $art->author_name)) }}">{{ $art->author_name }}</a>
</div>
<div class="font-caption text-caption text-ink-muted mt-0.5">{{ $art->author_role ?: 'विशेष संवाददाता' }}</div>
<div class="font-caption text-caption text-ink-muted mt-1">
<a class="hover:text-primary transition-colors" href="{{ url('author/'.rawurlencode((string) $art->author_name)) }}">इस लेखक के सभी लेख देखें →</a>
</div>
</div>
</div>

{{-- Key points --}}
@if($key_points->isNotEmpty())
<div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm border-l-4 border-primary">
<h2 class="font-headline-sm text-headline-sm font-bold text-ink-primary mb-3">मुख्य बिंदु</h2>
<ul class="space-y-2">
@foreach($key_points as $kp)
<li class="flex gap-2.5 font-body-md text-body-md text-ink-primary leading-relaxed"><span class="material-symbols-outlined text-[18px] text-primary shrink-0">check_circle</span><span>{!! $kp->point !!}</span></li>
@endforeach
</ul>
</div>
@endif

{{-- Body — sanitized on save by App\Support\Html. --}}
<div class="article-body font-body-md text-body-md text-ink-primary leading-relaxed [&_h2]:font-headline-sm [&_h2]:text-headline-sm [&_h2]:font-bold [&_h2]:mt-6 [&_h2]:mb-3 [&_h3]:font-headline-sm [&_h3]:font-bold [&_h3]:mt-4 [&_h3]:mb-2 [&_p]:mb-4 [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-4 [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:mb-4 [&_li]:mb-1.5 [&_img]:rounded-lg [&_img]:my-4 [&_img]:max-w-full [&_a]:text-primary [&_a]:underline [&_blockquote]:border-l-4 [&_blockquote]:border-primary [&_blockquote]:bg-surface-container-lowest [&_blockquote]:py-2 [&_blockquote]:px-4 [&_blockquote]:italic [&_blockquote]:my-4 [&_blockquote]:rounded-r-lg [&_hr]:my-6 [&_table]:w-full [&_table]:my-6 [&_table]:border-collapse [&_table]:text-sm [&_th]:border [&_th]:border-border-hairline [&_th]:bg-surface-cream [&_th]:p-2.5 [&_th]:text-left [&_th]:font-semibold [&_td]:border [&_td]:border-border-hairline [&_td]:p-2.5 [&_figure]:my-6 [&_figcaption]:text-xs [&_figcaption]:text-ink-muted [&_figcaption]:italic [&_figcaption]:mt-1.5 [&_pre]:bg-stone-900 [&_pre]:text-amber-100 [&_pre]:p-4 [&_pre]:rounded-lg [&_pre]:my-4 [&_pre]:overflow-x-auto [&_code]:font-mono [&_code]:text-sm [&_mark]:bg-yellow-200 [&_mark]:px-1 [&_mark]:rounded">
{!! $art->body !!}
</div>

{{-- Stat / read-time strip --}}
@if($art->stat_text)
<div class="bg-surface-cream rounded-xl p-4 font-caption text-caption text-ink-secondary flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-primary">insights</span>{{ $art->stat_text }}
</div>
@endif

</article>

@include('partials.sidebar')
</div>
@endsection
