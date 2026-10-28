@php
use App\Models\Article;
use Carbon\Carbon;

// Legacy archive.php: validate ?date (Y-m-d + checkdate), else today.
$date_in = trim((string) request('date', date('Y-m-d')));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_in)) {
    $date_in = date('Y-m-d');
}
[$yy, $mm, $dd] = array_map('intval', explode('-', $date_in));
if (!checkdate($mm, $dd, $yy)) {
    $date_in = date('Y-m-d');
}
$ts = strtotime($date_in);

$items = Article::published()
    ->where('published_at', '<=', now())
    ->whereDate('published_at', $date_in)
    ->with('category')
    ->orderByDesc('published_at')
    ->limit(50)
    ->get();

$prev = Carbon::createFromTimestamp($ts)->subDay()->format('Y-m-d');
$next = Carbon::createFromTimestamp($ts)->addDay()->format('Y-m-d');
$next_disabled = $next > date('Y-m-d');

$active_nav = '';
$page_title = 'पुरालेख: ' . \App\Support\Site::hiDateShort($ts) . ' | दैनिक विश्वामित्र';
$page_desc = 'दिनांकवार पुरालेख — ' . \App\Support\Site::hiDateShort($ts);

$__sb = App\Http\View\Composers\PublicLayoutComposer::sidebarData(null);
extract($__sb);
$is_politics = ($is_politics ?? false);
$archive = ($archive ?? null);
$sidebar_back = url('archive?date=' . urlencode($date_in));
@endphp

@extends('layouts.public')

@section('content')
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-8 flex flex-col gap-4">
<div class="bg-surface-container-lowest rounded-xl shadow-sm p-6">
<div class="flex items-center gap-3 mb-1">
<span class="w-1.5 h-6 bg-primary rounded"></span>
<div>
<h1 class="font-headline-lg text-headline-lg text-ink-primary">दिनांकवार पुरालेख</h1>
<p class="font-caption text-caption text-ink-secondary">{{ e(\App\Support\Site::hiDateShort($ts)) }} को प्रकाशित {{ \App\Support\Site::hiDigits((string) count($items)) }} समाचार</p>
</div>
</div>
<form method="get" class="mt-4 flex flex-wrap items-center gap-2">
<input type="date" name="date" value="{{ e($date_in) }}" max="{{ e(date('Y-m-d')) }}" class="px-3 py-2 text-sm bg-surface-container-low border border-border-hairline rounded focus:outline-none focus:border-primary text-ink-primary"/>
<button class="bg-primary text-on-primary px-5 py-2 rounded text-sm font-semibold hover:bg-masthead-crimson transition-colors" type="submit">देखें</button>
<a href="{{ url('archive?date=' . urlencode($prev)) }}" class="px-3 py-2 rounded bg-surface-container-low hover:bg-surface-container text-ink-primary text-sm flex items-center gap-1 transition-colors"><span class="material-symbols-outlined text-[16px]">chevron_left</span>पिछला दिन</a>
@if (!$next_disabled)<a href="{{ url('archive?date=' . urlencode($next)) }}" class="px-3 py-2 rounded bg-surface-container-low hover:bg-surface-container text-ink-primary text-sm flex items-center gap-1 transition-colors">अगला दिन<span class="material-symbols-outlined text-[16px]">chevron_right</span></a>@endif
</form>
</div>
@foreach ($items as $r)
@include('public.partials.story-row', ['r' => $r])
@endforeach
@if ($items->isEmpty())<div class="bg-surface-container-lowest p-10 rounded-lg text-center text-ink-secondary">इस तिथि को कोई समाचार प्रकाशित नहीं हुआ।</div>@endif
</div>
@include('partials.sidebar')</div>
</div></main>
@endsection
