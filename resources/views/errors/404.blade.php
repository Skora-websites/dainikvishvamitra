{{--
    Framework 404 for abort(404) — same branded card as public/404.blade.php.
    Laravel resolves errors/404.blade.php for aborts and ModelNotFound.
--}}
@php
    $page_title = 'पृष्ठ नहीं मिला | दैनिक विश्वामित्र';
    $page_desc = 'यह पृष्ठ उपलब्ध नहीं है या हटा दिया गया है।';
    $active_nav = '';
    $not_found_title = $not_found_title ?? 'पृष्ठ नहीं मिला';
    $not_found_msg = $not_found_msg ?? 'यह पृष्ठ उपलब्ध नहीं है या हटा दिया गया है।';
@endphp
@extends('layouts.public')

@section('content')
<div class="pt-44 w-full bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-16 text-center">
<div class="max-w-md mx-auto bg-surface-container-lowest rounded-xl shadow-sm p-10">
<span class="material-symbols-outlined text-ink-muted text-[56px]">search_off</span>
<h1 class="font-headline-lg text-headline-lg text-ink-primary mt-3">{{ e($not_found_title) }}</h1>
<p class="mt-2 font-body-md text-body-md text-ink-secondary">{{ e($not_found_msg) }}</p>
<a class="inline-flex items-center gap-1 mt-5 bg-primary text-on-primary px-5 py-2 rounded text-sm font-semibold hover:bg-masthead-crimson transition-colors" href="{{ url('/') }}"><span class="material-symbols-outlined text-[18px]">home</span>होम पर वापस जाएं</a>
</div>
</div></div>
@endsection
