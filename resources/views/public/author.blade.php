@php
use App\Models\Article;

// Legacy author.php: exact author_name match first, else a LIKE fallback
// (LIKE metacharacters escaped), newest first, LIMIT 30.
$name = trim((string) request('name', ''));
$items = collect();
if ($name !== '') {
    $items = Article::published()
        ->where('published_at', '<=', now())
        ->where('author_name', $name)
        ->with('category')
        ->orderByDesc('published_at')
        ->limit(30)
        ->get();

    if ($items->isEmpty()) {
        $like = '%' . addcslashes($name, "\\%_") . '%';
        $items = Article::published()
            ->where('published_at', '<=', now())
            ->where('author_name', 'like', $like)
            ->with('category')
            ->orderByDesc('published_at')
            ->limit(30)
            ->get();
    }
}

// Profile card comes from the first result (legacy $profile).
$profile = null;
if ($items->isNotEmpty()) {
    $first = $items->first();
    $profile = [
        'name' => $first->author_name,
        'role' => $first->author_role,
        'image' => $first->author_image_url,
    ];
}

$active_nav = '';
$page_title = ($profile['name'] ?? $name) . ' | दैनिक विश्वामित्र';
$page_desc = $profile['name'] ?? 'लेखक';

$__sb = App\Http\View\Composers\PublicLayoutComposer::sidebarData(null);
extract($__sb);
$is_politics = ($is_politics ?? false);
$archive = ($archive ?? null);
// Legacy passed the author page URL back to the poll form.
$sidebar_back = $name !== '' ? url('author/' . rawurlencode($name)) : '';
@endphp

@extends('layouts.public')

@section('content')
@if ($name === '' || $items->isEmpty())
    {{-- Legacy required 404.php in this branch. --}}
    @include('public.404', [
        'not_found_title' => $name === '' ? 'लेखक चुनें' : 'लेखक नहीं मिले',
        'not_found_msg' => $name === '' ? 'कृपया किसी लेख के लेखक नाम पर क्लिक करें।'
            : '“' . mb_substr($name, 0, 60) . '” के कोई लेख उपलब्ध नहीं हैं।',
    ])
@else
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-8 flex flex-col gap-4">
<div class="bg-surface-container-lowest rounded-xl shadow-sm p-6 flex items-center gap-4">
@if (!empty($profile['image']))
<img class="w-16 h-16 rounded-full object-cover shrink-0" alt="{{ e($profile['name']) }}" src="{{ e($profile['image']) }}"/>
@else
<div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-2xl shrink-0">{{ e(mb_substr($profile['name'] ?: 'वि', 0, 1)) }}</div>
@endif
<div>
<h1 class="font-headline-lg text-headline-lg text-ink-primary">{{ e($profile['name']) }}</h1>
<p class="font-caption text-caption text-ink-muted">{{ e($profile['role'] ?: 'दैनिक विश्वामित्र') }} · {{ \App\Support\Site::hiDigits((string) count($items)) }} लेख</p>
</div>
</div>
@foreach ($items as $r)
@include('public.partials.story-row', ['r' => $r])
@endforeach
</div>
@include('partials.sidebar')</div>
</div></main>
@endif
@endsection
