@extends('layouts.admin')

@section('content')
@php($rows = $rows ?? collect())
@php($edit = ($edit ?? null) ?? $rows->firstWhere('id', (int) request('edit')))
@php($videoPub = ($edit && $edit->published_at) ? $edit->published_at->format('Y-m-d\TH:i') : '')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 bg-white rounded-lg border divide-y text-sm">
@foreach(($rows ?? []) as $r)
<div class="px-4 py-2.5 flex items-center justify-between gap-3">
<div class="min-w-0 flex items-center gap-3">
@if(!empty($r->thumbnail))<img class="w-16 h-10 rounded object-cover shrink-0" alt="" src="{{ asset($r->thumbnail) }}"/>@endif
<div class="min-w-0">
<span class="text-xs {{ $r->is_active ? 'text-green-700' : 'text-stone-400' }}">{{ $r->is_active ? '● सक्रिय · Active' : '○ बंद · Inactive' }}</span>
@if(!empty($r->duration_text))<span class="text-xs text-stone-500"> · {{ $r->duration_text }}</span>@endif
<div class="truncate mt-0.5 font-medium">{{ $r->title }}</div>
<div class="text-xs text-stone-500">YouTube: {{ $r->youtube_id }}</div>
</div>
</div>
<div class="flex gap-2 shrink-0 text-xs">
<a class="text-stone-600 hover:underline" target="_blank" href="{{ url('video?play=' . $r->id) }}">देखें · View</a>
<a class="text-red-800 hover:underline" href="{{ route('admin.videos', ['edit' => $r->id]) }}">संपादित करें · Edit</a>
<form method="post" action="{{ route('admin.videos.delete', $r->id) }}" class="inline" onsubmit="return confirm('हटाएं? / Delete?')">@csrf<button class="text-red-600 hover:underline" type="submit">हटाएं · Delete</button></form>
</div>
</div>
@endforeach
@if(($rows ?? collect())->isEmpty())<div class="px-4 py-6 text-center text-stone-500">कोई वीडियो नहीं। · No videos.</div>@endif
</div>
<div class="bg-white rounded-lg border p-4 h-fit">
<h2 class="font-bold mb-3">{{ $edit ? 'वीडियो संपादित करें · Edit video' : 'नया वीडियो · New video' }}</h2>
<form method="post" enctype="multipart/form-data" action="{{ $edit ? route('admin.videos.update', $edit->id) : route('admin.videos.store') }}" class="space-y-3 text-sm">
@csrf
<input type="hidden" name="id" value="{{ (int) ($edit->id ?? 0) }}"/>
<div><label class="block text-xs font-semibold mb-1">शीर्षक · Title *</label><input name="title" required value="{{ old('title', $edit->title ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">YouTube लिंक या आईडी · YouTube link or ID * <span class="font-normal text-stone-500">(पूरा लिंक या 11 अक्षर · full link or 11 chars, जैसे dQw4w9WgXcQ)</span></label><input name="youtube_id" required value="{{ old('youtube_id', $edit->youtube_id ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('youtube_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">थंबनेल · Thumbnail <span class="font-normal text-stone-500">(रिक्त = YouTube थंबनेल · empty = YouTube thumbnail)</span></label>
@if(!empty($edit) && !empty($edit->thumbnail))<img class="w-32 h-20 rounded object-cover mb-1" alt="" src="{{ asset($edit->thumbnail) }}"/><label class="flex items-center gap-1.5 text-xs mb-1"><input type="checkbox" name="remove_thumbnail" value="1"/> थंबनेल हटाएं · Remove thumbnail</label>@endif
<input type="file" name="thumbnail" accept="image/*" data-max-kb="10240" data-label="थंबनेल · Thumbnail" class="w-full text-xs"/></div>
<div class="grid grid-cols-2 gap-2">
<div><label class="block text-xs font-semibold mb-1">अवधि · Duration <span class="font-normal text-stone-500">(जैसे ०४:३२ · e.g. 04:32)</span></label><input name="duration_text" maxlength="20" value="{{ old('duration_text', $edit->duration_text ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<div><label class="block text-xs font-semibold mb-1">क्रम · Order</label><input type="number" name="sort_order" value="{{ (int) old('sort_order', $edit->sort_order ?? 0) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
</div>
<div><label class="block text-xs font-semibold mb-1">विवरण · Description</label><textarea name="description" rows="3" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">{{ old('description', $edit->description ?? '') }}</textarea></div>
<div><label class="block text-xs font-semibold mb-1">प्रकाशन तिथि · Published at</label><input type="datetime-local" name="published_at" value="{{ old('published_at', $videoPub) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $edit->is_active ?? 1) ? 'checked' : '' }}/> सक्रिय · Active</label>
<div class="flex gap-2">
<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded font-semibold" type="submit">सहेजें · Save</button>
@if($edit)<a href="{{ route('admin.videos') }}" class="px-4 py-2 rounded border">रद्द करें · Cancel</a>@endif
</div>
</form>
</div>
</div>
@endsection
