@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg border p-4 mb-4">
<form method="GET" action="{{ route('admin.articles') }}" class="flex flex-wrap gap-2 items-end">
<div>
<label class="block text-xs font-semibold mb-1">Search</label>
<input name="q" value="{{ e($q ?? '') }}" class="px-3 py-1.5 border rounded focus:outline-none focus:border-red-800 text-sm w-56" placeholder="Title / author"/>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Status</label>
<select name="status" class="px-3 py-1.5 border rounded focus:outline-none focus:border-red-800 text-sm">
<option value="">All</option>
<option value="published" {{ ($status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
<option value="draft" {{ ($status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
</select>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Category</label>
<select name="cat" class="px-3 py-1.5 border rounded focus:outline-none focus:border-red-800 text-sm">
<option value="0">All</option>
@php($catFilter = (int) request('cat', 0))
@foreach(($cats ?? []) as $c)<option value="{{ (int) $c->id }}" {{ $catFilter === (int) $c->id ? 'selected' : '' }}>{{ $c->name_hi }}</option>@endforeach
</select>
</div>
<button class="bg-stone-800 text-white px-4 py-1.5 rounded text-sm">Apply</button>
<a href="{{ route('admin.articles.create') }}" class="bg-red-800 hover:bg-red-900 text-white px-4 py-1.5 rounded text-sm font-semibold">+ New</a>
</form>
</div>
<div class="bg-white rounded-lg border overflow-x-auto">
<table class="w-full text-sm min-w-[860px]">
<thead><tr class="text-left text-xs text-stone-500 border-b">
<th class="px-4 py-2">Thumbnail</th><th class="px-4 py-2">Title</th><th class="px-4 py-2">Category</th><th class="px-4 py-2">Language</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Views</th><th class="px-4 py-2">Updated</th><th class="px-4 py-2">Actions</th>
</tr></thead>
<tbody class="divide-y">
@foreach(($articles ?? []) as $r)
<tr>
<td class="px-4 py-2"><img src="{{ $r->image_url }}" alt="" class="w-14 h-10 object-cover rounded border bg-stone-100"/></td>
<td class="px-4 py-2"><div class="font-medium">{{ $r->title }}</div><div class="text-xs text-stone-500">{{ $r->slug }}@if(!empty($r->home_slot) || !empty($r->cat_slot)) · <span class="text-red-700 font-semibold">[{{ trim(($r->home_slot ?? '') . ' ' . ($r->cat_slot ?? '')) }}]</span>@endif</div></td>
<td class="px-4 py-2 text-xs">{{ $r->category->name_hi ?? '' }}</td>
<td class="px-4 py-2 text-xs">{{ $r->language === 'en' ? 'English' : 'Hindi' }}</td>
<td class="px-4 py-2"><span class="text-xs px-2 py-0.5 rounded {{ $r->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-stone-200 text-stone-600' }}">{{ $r->status === 'published' ? 'Published' : 'Draft' }}</span></td>
<td class="px-4 py-2">{{ number_format((int) $r->views) }}</td>
<td class="px-4 py-2 text-xs text-stone-500">{{ $r->updated_at ? $r->updated_at->format('d M Y H:i') : '' }}</td>
<td class="px-4 py-2 whitespace-nowrap">
<a class="text-red-800 hover:underline text-xs mr-2" href="{{ route('admin.articles.edit', $r->id) }}">Edit</a>
<a class="text-stone-500 hover:underline text-xs mr-2" target="_blank" href="{{ url('article/' . $r->slug) }}">View</a>
<form method="post" action="{{ route('admin.articles.delete', $r->id) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf<button class="text-red-600 hover:underline text-xs" type="submit">Delete</button></form>
</td>
</tr>
@endforeach
@if(($articles ?? collect())->isEmpty())<tr><td colspan="8" class="px-4 py-6 text-center text-stone-500">No articles found.</td></tr>@endif
</tbody>
</table>
</div>
@if(method_exists($articles ?? null, 'links'))
<div class="mt-3">{{ $articles->links() }}</div>
@endif
@endsection
