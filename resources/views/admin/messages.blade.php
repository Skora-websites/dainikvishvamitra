@extends('layouts.admin')

@section('content')
@php($rows = $rows ?? collect())
@php($unread = $unread ?? 0)
@php($view = ($view ?? null) ?? $rows->firstWhere('id', (int) request('view')))
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 bg-white rounded-lg border divide-y text-sm">
@foreach(($rows ?? []) as $r)
<div class="px-4 py-2.5 flex items-center justify-between gap-3 {{ !$r->is_read ? 'bg-amber-50' : '' }}">
<div class="min-w-0">
<div class="font-medium truncate">{{ $r->is_read ? '' : '● ' }}{{ $r->name }} <span class="font-normal text-stone-500">&lt;{{ $r->email }}&gt;</span></div>
<div class="text-xs text-stone-500 truncate">{{ $r->subject ?: '(बिना विषय · no subject)' }} · {{ $r->created_at }}</div>
</div>
<div class="flex gap-2 shrink-0 text-xs">
<a class="text-red-800 hover:underline" href="{{ route('admin.messages', ['view' => $r->id]) }}">पढ़ें · Read</a>
<form method="post" action="{{ route('admin.messages.delete', $r->id) }}" class="inline" onsubmit="return confirm('हटाएं? / Delete?')">@csrf<button class="text-red-600 hover:underline" type="submit">हटाएं · Delete</button></form>
</div>
</div>
@endforeach
@if(($rows ?? collect())->isEmpty())<div class="px-4 py-6 text-center text-stone-500">कोई संदेश नहीं। · No messages.</div>@endif
</div>
<div class="bg-white rounded-lg border p-4 h-fit text-sm">
<div class="flex items-center justify-between mb-3">
<h2 class="font-bold">संदेश विवरण · Message details</h2>
@if(($unread ?? 0) > 0)<form method="post" action="{{ route('admin.messages.read_all') }}">@csrf<button class="text-xs text-stone-600 hover:underline" type="submit">सभी पढ़ा हुआ · Mark all read ({{ \App\Support\Site::hiDigits((string) $unread) }})</button></form>@endif
</div>
@if($view)
<div class="space-y-2">
<div><span class="text-xs font-semibold text-stone-500">नाम · Name:</span> {{ $view->name }}</div>
<div><span class="text-xs font-semibold text-stone-500">ईमेल · Email:</span> <a class="text-red-800 hover:underline" href="mailto:{{ $view->email }}">{{ $view->email }}</a></div>
@if(!empty($view->phone))<div><span class="text-xs font-semibold text-stone-500">फ़ोन · Phone:</span> {{ $view->phone }}</div>@endif
@if(!empty($view->subject))<div><span class="text-xs font-semibold text-stone-500">विषय · Subject:</span> {{ $view->subject }}</div>@endif
<div><span class="text-xs font-semibold text-stone-500">दिनांक · Date:</span> {{ $view->created_at }}</div>
<div class="pt-2 border-t whitespace-pre-wrap">{{ $view->message }}</div>
<div class="pt-3 flex gap-2">
@if(!$view->is_read)
<form method="post" action="{{ route('admin.messages.read', $view->id) }}">@csrf<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded text-xs font-semibold" type="submit">पढ़ा हुआ चिह्नित करें · Mark as read</button></form>
@endif
<a class="px-4 py-2 rounded border text-xs" href="mailto:{{ $view->email }}?subject=Re: {{ rawurlencode($view->subject ?: 'आपका संदेश · your message') }}">उत्तर दें · Reply</a>
</div>
</div>
@else
<p class="text-stone-500">बाईं सूची से कोई संदेश चुनें। · Pick a message from the list.</p>
@endif
</div>
</div>
@if(method_exists($rows ?? null, 'links'))
<div class="mt-3">{{ $rows->links() }}</div>
@endif
@endsection
