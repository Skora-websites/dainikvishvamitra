@php
use App\Models\Article;

// Legacy search.php: LIKE over title/excerpt/body/author_name, published only,
// published_at <= NOW(), newest first, LIMIT 20.
$q = trim((string) request('q', ''));
$results = collect();
if ($q !== '') {
    $like = '%' . $q . '%';
    $results = Article::published()
        ->where('published_at', '<=', now())
        ->where(function ($w) use ($like) {
            $w->where('title', 'like', $like)
                ->orWhere('excerpt', 'like', $like)
                ->orWhere('body', 'like', $like)
                ->orWhere('author_name', 'like', $like);
        })
        ->with('category')
        ->orderByDesc('published_at')
        ->limit(20)
        ->get();
}

$active_nav = '';
$page_title = ($q !== '' ? 'खोज: ' . $q : 'खोजें') . ' | दैनिक विश्वामित्र';
$page_desc = 'दैनिक विश्वामित्र में समाचार, शहर या लेखक खोजें।';

$__sb = App\Http\View\Composers\PublicLayoutComposer::sidebarData(null);
extract($__sb);
$is_politics = ($is_politics ?? false);
$archive = ($archive ?? null);
$sidebar_back = '';
@endphp

@extends('layouts.public')

@section('content')
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-8 flex flex-col gap-4">
<div class="bg-surface-container-lowest rounded-lg shadow-sm p-6">
<div class="flex items-center gap-3 mb-4">
<span class="material-symbols-outlined text-primary text-[24px]">search</span>
<h1 class="font-headline-md text-headline-md text-ink-primary">@if ($q !== '')“{{ e($q) }}” हेतु {{ \App\Support\Site::hiDigits((string) count($results)) }} परिणाम@elseसमाचार खोजें@endif</h1>
</div>
<form action="{{ url('search') }}" method="get" class="flex gap-2">
<input name="q" value="{{ e($q) }}" placeholder="समाचार, शहर या लेखक खोजें..." class="flex-1 px-3 py-2 text-sm bg-surface-container-low border border-border-hairline rounded focus:outline-none focus:border-primary text-ink-primary"/>
<button class="bg-primary text-on-primary px-5 py-2 rounded text-sm font-semibold hover:bg-masthead-crimson transition-colors" type="submit">खोजें</button>
</form>
</div>
@foreach ($results as $r)
@include('public.partials.story-row', ['r' => $r])
@endforeach
@if ($q !== '' && $results->isEmpty())
<div class="bg-surface-container-lowest p-8 rounded-lg text-center text-ink-secondary">कोई परिणाम नहीं मिला। कृपया अन्य शब्दों से खोजें।</div>
@endif
</div>
@include('partials.sidebar')</div>
</div></main>
@endsection
