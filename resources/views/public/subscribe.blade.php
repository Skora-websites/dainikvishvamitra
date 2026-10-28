@php
    // Message/feedback comes from the session (set by SubscribeController).
    $msg = session('err') ?: session('ok');
    $ok = (bool) session('ok');

    $active_nav = '';
    $page_title = 'सदस्यता लें | दैनिक विश्वामित्र';
    $page_desc = 'दिन की शीर्ष खबरें सीधे अपने इनबॉक्स में निःशुल्क प्राप्त करें।';
@endphp

@extends('layouts.public')

@section('content')
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-10">
<div class="max-w-xl mx-auto bg-surface-container-lowest rounded-xl shadow-sm p-8">
<div class="flex items-center gap-3 mb-2">
<span class="w-1.5 h-6 bg-primary rounded"></span>
<h1 class="font-headline-lg text-headline-lg text-ink-primary">दैनिक समाचार बुलेटिन</h1>
</div>
<p class="font-body-md text-body-md text-ink-secondary mb-6">दिन की शीर्ष खबरें सीधे अपने इनबॉक्स में निःशुल्क प्राप्त करें। हर सुबह ८ बजे।</p>
@if ($msg)
<div class="mb-4 p-3 rounded text-sm font-semibold @if($ok) bg-market-up/10 text-market-up @else bg-market-down/10 text-market-down @endif">{{ e($msg) }}</div>
@endif
<form class="space-y-3" method="post">
@csrf
<input name="email" required type="email" placeholder="आपका ईमेल पता" class="w-full px-3 py-2.5 text-sm bg-surface-container-low border border-border-hairline rounded focus:outline-none focus:border-primary text-ink-primary"/>
<button class="w-full bg-primary text-on-primary py-2.5 rounded text-sm font-semibold hover:bg-masthead-crimson transition-colors" type="submit">सदस्यता प्रारंभ करें</button>
</form>
</div>
</div></main>
@endsection
