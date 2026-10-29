@extends('layouts.admin')

@section('content')
@php
$stats = $stats ?? [];
$cards = [
    ['Published articles', $stats['published'] ?? 0, 'article', 'admin.articles', null],
    ['Drafts', $stats['drafts'] ?? 0, 'edit_note', 'admin.articles', ['status' => 'draft']],
    ['Categories', $stats['categories'] ?? 0, 'category', 'admin.categories', null],
    ['Subscribers', $stats['subs'] ?? 0, 'mail', 'admin.subscribers', null],
    ['Total views', $stats['views'] ?? 0, 'visibility', 'admin.articles', null],
    ['Unread messages', $stats['unread'] ?? 0, 'inbox', 'admin.messages', null],
];
@endphp
<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
@foreach($cards as [$label, $n, $icon, $route, $params])
<a href="{{ route($route, $params ?? []) }}" class="bg-white rounded-lg border p-4 hover:shadow transition-shadow">
<div class="flex items-center gap-2 text-stone-500 text-xs"><span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>{{ $label }}</div>
<div class="text-3xl font-bold mt-1">{{ number_format((int) $n) }}</div>
</a>
@endforeach
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<div class="lg:col-span-2 bg-white rounded-lg border">
<div class="flex items-center justify-between mb-3 px-4 pt-4">
<h2 class="font-bold flex items-center gap-2"><span class="material-symbols-outlined text-red-700" aria-hidden="true">article</span>Latest articles</h2>
<a href="{{ route('admin.articles.create') }}" class="text-sm bg-red-800 hover:bg-red-900 text-white px-3 py-1.5 rounded font-semibold">+ New</a>
</div>
<div class="divide-y text-sm">
@foreach(($recent ?? collect()) as $l)
<div class="px-4 py-2.5 flex items-center justify-between gap-3">
<div class="min-w-0">
<span class="text-xs text-stone-500">{{ $l->category->name_hi ?? '' }} · {{ $l->published_at ? $l->published_at->format('d M Y H:i') : '' }}</span>
<div class="truncate font-medium">{{ $l->title }}</div>
</div>
<div class="flex items-center gap-2 shrink-0">
<span class="text-xs px-2 py-0.5 rounded {{ $l->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-stone-200 text-stone-600' }}">{{ $l->status === 'published' ? 'Published' : 'Draft' }}</span>
<a class="text-red-800 hover:underline text-xs" href="{{ route('admin.articles.edit', $l->id) }}">Edit</a>
</div>
</div>
@endforeach
@if(($recent ?? collect())->isEmpty())<div class="px-4 py-6 text-center text-stone-500">No articles.</div>@endif
</div>
</div>

<div class="bg-white rounded-lg border">
<div class="flex items-center justify-between mb-3 px-4 pt-4">
<h2 class="font-bold flex items-center gap-2"><span class="material-symbols-outlined text-amber-700" aria-hidden="true">inbox</span>Recent messages</h2>
<a href="{{ route('admin.messages') }}" class="text-xs text-stone-600 hover:underline">View all →</a>
</div>
<div class="divide-y text-sm">
@foreach(($latestMessages ?? collect()) as $m)
<div class="px-4 py-2.5 flex items-center justify-between gap-3 {{ !$m->is_read ? 'bg-amber-50/60' : '' }}">
<div class="min-w-0">
<div class="font-medium truncate text-xs">{{ !$m->is_read ? '● ' : '' }}{{ $m->name }}</div>
<div class="truncate text-stone-600 text-xs">{{ $m->subject ?: $m->message }}</div>
<div class="text-[11px] text-stone-400">{{ optional($m->created_at)->diffForHumans() }}</div>
</div>
<a class="text-red-800 hover:underline text-xs shrink-0" href="{{ route('admin.messages', ['view' => $m->id]) }}">Read</a>
</div>
@endforeach
@if(($latestMessages ?? collect())->isEmpty())<div class="px-4 py-6 text-center text-stone-500">No messages.</div>@endif
</div>
</div>
</div>
@endsection
