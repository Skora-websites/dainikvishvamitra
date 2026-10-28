@php
use App\Models\HoroscopeSign;
use App\Support\Site;

$active_nav = 'horoscope';
$page_title = 'आज का राशिफल | दैनिक विश्वामित्र';

$signs = HoroscopeSign::orderBy('sort_order')->orderBy('id')->limit(12)->get();

$__sb = App\Http\View\Composers\PublicLayoutComposer::sidebarData(null);
extract($__sb);
$is_politics = ($is_politics ?? false);
$sidebar_back = '';
@endphp

@extends('layouts.public')

@section('content')
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-8 flex flex-col gap-4">
<div class="bg-surface-cream rounded-xl p-6 shadow-sm">
<div class="flex items-center gap-3 mb-1">
<span class="w-1.5 h-6 bg-primary rounded"></span>
<div>
<h1 class="font-headline-lg text-headline-lg text-ink-primary">आज का दैनिक राशिफल</h1>
<p class="font-caption text-caption text-ink-secondary">{{ Site::setting('edition_date', Site::hiDateShort()) }} | पंडितजी द्वारा ग्रह दशा का विश्लेषण</p>
</div>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
@forelse ($signs as $s)
<div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm">
<div class="flex items-center gap-3 mb-2">
<div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0 font-bold text-xl">{{ $s->symbol }}</div>
<div>
<h2 class="font-headline-sm text-headline-sm text-ink-primary font-bold">{{ $s->name_hi }} राशि ({{ $s->name_en }})</h2>
</div>
</div>
<p class="font-body-md text-body-md text-ink-secondary leading-relaxed">{{ $s->prediction }}</p>
<div class="flex flex-wrap items-center gap-x-4 gap-y-1 pt-3 text-xs font-caption text-ink-muted">
<span>शुभ रंग: <strong class="text-ink-primary">{{ $s->lucky_color }}</strong></span>
<span>शुभ अंक: <strong class="text-ink-primary">{{ $s->lucky_number }}</strong></span>
<span>आराध्य: <strong class="text-ink-primary">{{ $s->deity }}</strong></span>
</div>
</div>
@empty
<div class="md:col-span-2 bg-surface-container-lowest p-10 rounded-xl text-center text-ink-secondary">राशिफल शीघ्र उपलब्ध होगा।</div>
@endforelse
</div>
</div>
@include('partials.sidebar')
</div>
</div></main>
@endsection
