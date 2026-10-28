{{--
    Render-only fallback for the poll vote POST endpoint.
    The parent's controller owns the vote/redirect logic; this view renders when
    the controller answers a GET/invalid request instead of redirecting.
    Mirrors the legacy poll-vote.php messages.
--}}
@php
$active_nav = 'home';
$page_title = 'मतदान | दैनिक विश्वामित्र';
$page_desc = 'दैनिक जनमत पोल में अपनी राय दर्ज करें।';

$message = $message ?? 'अमान्य अनुरोध (CSRF)। कृपया वापस जाकर पुनः प्रयास करें।';
@endphp

@extends('layouts.public')

@section('content')
<main class="w-full pt-44 bg-surface"><div class="max-w-3xl mx-auto px-4 lg:px-8 py-12">
<div class="bg-surface-container-lowest rounded-xl shadow-sm p-8 text-center">
<span class="material-symbols-outlined text-primary text-[44px]">how_to_vote</span>
<h1 class="font-headline-lg text-headline-lg text-ink-primary mt-3">दैनिक जनमत पोल</h1>
<p class="font-body-md text-body-md text-ink-secondary mt-3">{{ $message }}</p>
<div class="pt-6 flex items-center justify-center gap-3">
<a href="{{ url('/') }}" class="bg-primary hover:bg-masthead-crimson text-on-primary px-5 py-2 rounded text-sm font-semibold transition-colors">मुख्य पृष्ठ</a>
<a href="{{ url()->previous() }}" class="bg-surface-container-low hover:bg-surface-container text-ink-primary px-5 py-2 rounded text-sm font-semibold transition-colors">वापस जाएँ</a>
</div>
</div>
</div></main>
@endsection
