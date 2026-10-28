@extends('layouts.admin')

@section('content')
@php($rows = $rows ?? collect())
@php($edit = ($edit ?? null) ?? $rows->firstWhere('id', (int) request('edit')))
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 bg-white rounded-lg border overflow-x-auto">
<table class="w-full text-sm min-w-[520px]">
<thead><tr class="text-left text-xs text-stone-500 border-b"><th class="px-4 py-2">नाम · Name</th><th class="px-4 py-2">स्लग · Slug</th><th class="px-4 py-2">समाचार · Articles</th><th class="px-4 py-2">नेव / फ़ुटर · Nav / footer</th><th class="px-4 py-2">कार्य · Actions</th></tr></thead>
<tbody class="divide-y">
@foreach(($rows ?? []) as $r)
<tr>
<td class="px-4 py-2 font-medium">{{ $r->name_hi }}</td>
<td class="px-4 py-2 text-xs text-stone-500">{{ $r->slug }}</td>
<td class="px-4 py-2">{{ \App\Support\Site::hiDigits((string) $r->articles_count) }}</td>
<td class="px-4 py-2 text-xs">{{ $r->show_in_nav ? 'नेव ✓ · Nav' : '— · None' }} / {{ $r->show_in_footer ? 'फ़ुटर ✓ · Footer' : '— · None' }}</td>
<td class="px-4 py-2 whitespace-nowrap">
<a class="text-red-800 hover:underline text-xs mr-2" href="{{ route('admin.categories', ['edit' => $r->id]) }}">संपादित करें · Edit</a>
<form method="post" action="{{ route('admin.categories.delete', $r->id) }}" class="inline" onsubmit="return confirm('हटाएं? / Delete?')">@csrf<button class="text-red-600 hover:underline text-xs" type="submit">हटाएं · Delete</button></form>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="bg-white rounded-lg border p-4 h-fit">
<h2 class="font-bold mb-3">{{ $edit ? 'संपादित करें · Edit' : 'नई श्रेणी · New' }}</h2>
<form method="post" action="{{ $edit ? route('admin.categories.update', $edit->id) : route('admin.categories.store') }}" class="space-y-3 text-sm">
@csrf
<input type="hidden" name="id" value="{{ (int) ($edit->id ?? 0) }}"/>
<input type="hidden" name="accent_color" value="{{ old('accent_color', $edit->accent_color ?? 'bg-primary') }}"/>
<div><label class="block text-xs font-semibold mb-1">नाम (हिंदी) · Name (Hindi) *</label><input name="name_hi" required value="{{ old('name_hi', $edit->name_hi ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('name_hi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">स्लग <span class="font-normal text-stone-500">(खाली = स्वतः · empty = auto)</span></label><input name="slug" value="{{ old('slug', $edit->slug ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('slug')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">विवरण · Description</label><input name="description" value="{{ old('description', $edit->description ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<div><label class="block text-xs font-semibold mb-1">उप-पट्टियाँ · Pills <span class="font-normal text-stone-500">(अल्पविराम से अलग · comma separated)</span></label><textarea name="pills" rows="3" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">{{ old('pills', $edit->pills ?? '') }}</textarea></div>
@php($status = old('status', $edit->status ?? ''))
<div class="grid grid-cols-2 gap-2">
<div><label class="block text-xs font-semibold mb-1">क्रम · Order</label><input type="number" name="sort_order" value="{{ (int) old('sort_order', $edit->sort_order ?? 0) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<div><label class="block text-xs font-semibold mb-1">स्थिति · Status</label><select name="status" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"><option value="active" {{ $status !== 'hidden' ? 'selected' : '' }}>सक्रिय · Active</option><option value="hidden" {{ $status === 'hidden' ? 'selected' : '' }}>छिपी · Hidden</option></select></div>
</div>
<label class="flex items-center gap-1.5"><input type="checkbox" name="show_in_nav" value="1" {{ old('show_in_nav', $edit->show_in_nav ?? 1) ? 'checked' : '' }}/> नेवबार में दिखाएं · Show in navbar</label>
<label class="flex items-center gap-1.5"><input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer', $edit->show_in_footer ?? 0) ? 'checked' : '' }}/> फ़ुटर में दिखाएं · Show in footer</label>
<div class="flex gap-2">
<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded font-semibold" type="submit">सहेजें · Save</button>
@if($edit)<a href="{{ route('admin.categories') }}" class="px-4 py-2 rounded border">रद्द करें · Cancel</a>@endif
</div>
</form>
</div>
</div>
@endsection
