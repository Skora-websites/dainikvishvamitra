@php
use App\Models\Page;

// Legacy contact.php: the "contact" page body is an editable intro blurb
// rendered through clean_body() (allowlist + javascript:/on* stripping).
$clean_body = static function (string $html): string {
    $allowed = '<p><br><b><strong><i><em><u><h2><h3><h4><ul><ol><li><blockquote><a><img><figure><figcaption><hr><span><div>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*"\s*javascript:[^"]*"/i', '$1="#"', $html);
    $html = preg_replace("/(href|src)\s*=\s*'\s*javascript:[^']*'/i", "$1='#'", $html);
    $html = preg_replace('/(href|src)\s*=\s*javascript:[^\s>]+/i', '$1="#"', $html);

    return (string) $html;
};

$info = Page::where('slug', 'contact')->where('status', 'published')->first();

// Message/feedback comes from the session (set by ContactFormController).
$msg = session('err') ?: session('ok');
$ok = (bool) session('ok');
$errors = $errors ?? null;
$is_politics = false;

$active_nav = '';
$page_title = 'संपर्क करें | दैनिक विश्वामित्र';
$page_desc = 'विज्ञापन, समाचार सुझाव या प्रतिक्रिया हेतु संपर्क करें।';

$__sb = App\Http\View\Composers\PublicLayoutComposer::sidebarData(null);
extract($__sb);
$archive = ($archive ?? null);
$sidebar_back = url('contact');
@endphp

@extends('layouts.public')

@section('content')
<main class="w-full pt-44 bg-surface"><div class="max-w-7xl mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-8 flex flex-col gap-4">
<div class="bg-surface-container-lowest rounded-xl shadow-sm p-6 sm:p-8">
<div class="flex items-center gap-3 mb-2">
<span class="w-1.5 h-6 bg-primary rounded"></span>
<h1 class="font-headline-lg text-headline-lg text-ink-primary">संपर्क करें</h1>
</div>
<p class="font-body-md text-body-md text-ink-secondary mb-4">विज्ञापन, समाचार सुझाव या प्रतिक्रिया हेतु नीचे फ़ॉर्म भरें। फ़ोन: {{ e(\App\Support\Site::setting('contact_phone', '')) }} · ईमेल: {{ e(\App\Support\Site::setting('contact_email', '')) }}</p>
@if ($info && trim(strip_tags($info->body ?? '')) !== '' && trim(strip_tags($info->body)) !== 'सामग्री शीघ्र प्रकाशित होगी।')
<div class="font-body-md text-body-md text-ink-secondary leading-relaxed mb-6 article-body">{!! $clean_body((string) $info->body) !!}</div>
@endif
@if ($msg)
<div class="mb-4 p-3 rounded text-sm font-semibold @if($ok) bg-market-up/10 text-market-up @else bg-market-down/10 text-market-down @endif">{{ e($msg) }}</div>
@endif
@if ($errors && $errors->any())
<div class="mb-4 p-3 rounded text-sm bg-red-50 text-red-800 border border-red-200">
<ul class="list-disc list-inside space-y-0.5">
@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
</ul>
</div>
@endif
@if (!$ok)
<form method="post" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
@csrf
<input type="text" name="website" value="" autocomplete="off" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;opacity:0;height:0;"/>
<div><label class="block text-xs font-semibold mb-1 text-ink-primary">नाम *</label><input name="name" required maxlength="120" value="" placeholder="आपका नाम" class="w-full px-3 py-2.5 text-sm bg-surface-container-low border border-border-hairline rounded focus:outline-none focus:border-primary text-ink-primary"/></div>
<div><label class="block text-xs font-semibold mb-1 text-ink-primary">ईमेल *</label><input name="email" required type="email" value="" placeholder="you@example.com" class="w-full px-3 py-2.5 text-sm bg-surface-container-low border border-border-hairline rounded focus:outline-none focus:border-primary text-ink-primary"/></div>
<div><label class="block text-xs font-semibold mb-1 text-ink-primary">फ़ोन</label><input name="phone" maxlength="30" value="" placeholder="+91 …" class="w-full px-3 py-2.5 text-sm bg-surface-container-low border border-border-hairline rounded focus:outline-none focus:border-primary text-ink-primary"/></div>
<div><label class="block text-xs font-semibold mb-1 text-ink-primary">विषय</label><input name="subject" maxlength="160" value="" placeholder="संदेश का विषय" class="w-full px-3 py-2.5 text-sm bg-surface-container-low border border-border-hairline rounded focus:outline-none focus:border-primary text-ink-primary"/></div>
<div class="sm:col-span-2"><label class="block text-xs font-semibold mb-1 text-ink-primary">संदेश *</label><textarea name="message" required rows="5" placeholder="अपना संदेश लिखें…" class="w-full px-3 py-2.5 text-sm bg-surface-container-low border border-border-hairline rounded focus:outline-none focus:border-primary text-ink-primary"></textarea></div>
<div class="sm:col-span-2"><button class="bg-primary text-on-primary px-6 py-2.5 rounded text-sm font-semibold hover:bg-masthead-crimson transition-colors" type="submit">संदेश भेजें</button></div>
</form>
@endif
</div>
</div>
@include('partials.sidebar')</div>
</div></main>
<style>.article-body p{margin-bottom:1rem}.article-body a{color:#9b000e;text-decoration:underline}</style>
@endsection
