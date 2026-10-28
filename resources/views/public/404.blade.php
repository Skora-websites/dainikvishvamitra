@php
    // Legacy 404.php is also included by author.php/page.php as an error state,
    // so $not_found_title / $not_found_msg arrive from the caller when present.
    $page_title = ($not_found_title ?? 'पृष्ठ नहीं मिला') . ' | दैनिक विश्वामित्र';
    $page_desc = $not_found_msg ?? 'यह पृष्ठ उपलब्ध नहीं है या हटा दिया गया है।';
    $active_nav = '';
    http_response_code(404);
@endphp
@extends('layouts.public')

@section('content')
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-16 text-center">
<div class="max-w-md mx-auto bg-surface-container-lowest rounded-xl shadow-sm p-10">
<span class="material-symbols-outlined text-ink-muted text-[56px]">search_off</span>
<h1 class="font-headline-lg text-headline-lg text-ink-primary mt-3">{{ e($not_found_title ?? 'पृष्ठ नहीं मिला') }}</h1>
<p class="mt-2 font-body-md text-body-md text-ink-secondary">{{ e($not_found_msg ?? 'यह पृष्ठ उपलब्ध नहीं है या हटा दिया गया है।') }}</p>
<a class="inline-flex items-center gap-1 mt-5 bg-primary text-on-primary px-5 py-2 rounded text-sm font-semibold hover:bg-masthead-crimson transition-colors" href="{{ url('/') }}"><span class="material-symbols-outlined text-[18px]">home</span>होम पर वापस जाएं</a>
</div>
</div></main>
@endsection
