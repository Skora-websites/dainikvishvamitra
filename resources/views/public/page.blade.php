@php
use App\Models\Page;

// DOM-based allowlist sanitizer (shared with the admin save path).
$clean_body = static fn (string $html): string => \App\Support\Html::clean($html);

// Legacy page.php: ?slug=, published only; else the shared 404 state.
$slug = trim((string) request('slug', ''));
$pg = $slug !== '' ? Page::where('slug', $slug)->where('status', 'published')->first() : null;

$active_nav = '';
$page_title = ($pg->title ?? 'पृष्ठ') . ' | दैनिक विश्वामित्र';
$page_desc = 'दैनिक विश्वामित्र';

$__sb = App\Http\View\Composers\PublicLayoutComposer::sidebarData(null);
extract($__sb);
$is_politics = ($is_politics ?? false);
$archive = ($archive ?? null);
$sidebar_back = $pg ? url('page/' . $pg->slug) : '';
@endphp

@extends('layouts.public')

@section('content')
@if (!$pg)
    @include('public.404', [
        'not_found_title' => 'पृष्ठ नहीं मिला',
        'not_found_msg' => 'यह पृष्ठ उपलब्ध नहीं है या हटा दिया गया है।',
    ])
@else
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-8">
<article class="bg-surface-container-lowest rounded-lg shadow-sm p-6 sm:p-8">
<div class="flex items-center gap-3 mb-4">
<span class="w-1.5 h-6 bg-primary rounded"></span>
<h1 class="font-headline-lg text-headline-lg text-ink-primary">{{ e($pg->title) }}</h1>
</div>
<div class="font-body-lg text-body-lg text-ink-secondary leading-relaxed space-y-4 article-body">
{!! $clean_body((string) $pg->body) !!}
</div>
</article>
</div>
@include('partials.sidebar')</div>
</div></main>
<style>
.article-body p{margin-bottom:1rem}
.article-body a{color:#9b000e;text-decoration:underline}
.article-body table{width:100%;border-collapse:collapse;margin:1.5rem 0;font-size:0.925rem}
.article-body th,.article-body td{border:1px solid #e7e5e4;padding:0.6rem 0.8rem;text-align:left}
.article-body th{background:#fafaf9;font-weight:600}
.article-body figure{margin:1.5rem 0;text-align:center}
.article-body figure img{max-width:100%;border-radius:0.5rem;display:inline-block}
.article-body figcaption{font-size:0.8rem;color:#78716c;font-style:italic;margin-top:0.35rem}
.article-body blockquote{border-left:4px solid #991b1b;background:#fef2f2;padding:0.6rem 1rem;border-radius:0 6px 6px 0;font-style:italic;margin:1.25rem 0}
.article-body mark{background:#fef08a;padding:0 2px;border-radius:2px}
.article-body pre{background:#1c1917;color:#fef3c7;padding:0.85rem 1.1rem;border-radius:6px;overflow-x:auto;font-size:0.875rem}
</style>
@endif
@endsection
