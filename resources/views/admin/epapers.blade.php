@extends('layouts.admin')

@section('content')
@php($rows = $rows ?? collect())
@php($edit = ($edit ?? null) ?? $rows->firstWhere('id', (int) request('edit')))
@php($editionDate = old('edition_date', ($edit && $edit->edition_date) ? $edit->edition_date->format('Y-m-d') : date('Y-m-d')))
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 bg-white rounded-lg border divide-y text-sm">
@foreach($rows as $r)
<div class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-stone-50 transition-colors">
<div class="min-w-0">
<div class="font-semibold text-stone-900">{{ $r->title }}</div>
<div class="text-xs text-stone-500 mt-0.5">{{ $r->edition_date ? $r->edition_date->format('Y-m-d') : '' }} · {{ $r->is_active ? 'सक्रिय · Active' : 'बंद · Inactive' }}</div>
</div>
<div class="flex items-center gap-2.5 shrink-0 text-xs">
<a class="inline-flex items-center gap-1 text-red-800 hover:text-red-900 font-semibold hover:underline" target="_blank" href="{{ route('epaper.show', $r->id) }}">
<span class="material-symbols-outlined text-[15px]">menu_book</span>
<span>पढ़ें · Read</span>
</a>
<a class="text-stone-600 hover:text-stone-900 hover:underline" target="_blank" download href="{{ asset($r->pdf_path) }}">
<span>PDF</span>
</a>
<span class="text-stone-300">|</span>
<a class="text-stone-700 hover:text-red-800 hover:underline font-medium" href="{{ route('admin.epapers', ['edit' => $r->id]) }}">संपादित करें · Edit</a>
<form method="post" action="{{ route('admin.papers.delete', $r->id) }}" class="inline" onsubmit="return confirm('हटाएं? / Delete?')">@csrf<button class="text-red-600 hover:underline" type="submit">हटाएं · Delete</button></form>
</div>
</div>
@endforeach
@if($rows->isEmpty())<div class="px-4 py-8 text-center text-stone-500">कोई अंक नहीं। पहला PDF अपलोड करें। · No editions yet. Upload your first PDF.</div>@endif
</div>
<div class="bg-white rounded-lg border p-4 h-fit">
<h2 class="font-bold mb-3">{{ $edit ? 'संपादित करें · Edit' : 'नया अंक · New edition' }}</h2>
<form method="post" enctype="multipart/form-data" action="{{ $edit ? route('admin.papers.update', $edit->id) : route('admin.papers.store') }}" class="space-y-3 text-sm">
@csrf
<input type="hidden" name="id" value="{{ (int) ($edit->id ?? 0) }}"/>
<div><label class="block text-xs font-semibold mb-1">शीर्षक · Title *</label><input name="title" required value="{{ old('title', $edit->title ?? '') }}" placeholder="जैसे: ९ अक्टूबर २०२५ — कोलकाता संस्करण · e.g. 9 Oct 2025 — Kolkata edition" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">तिथि · Date *</label><input type="date" name="edition_date" required value="{{ $editionDate }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('edition_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div>
<label class="block text-xs font-semibold mb-1">PDF फ़ाइल · PDF file {{ $edit ? '(बदलने हेतु नई चुनें · choose new to replace)' : '(सर्वोचम 100MB · max 100MB) *' }}</label>
@if($edit && $edit->pdf_path)
<div class="mb-2 p-2 rounded border border-stone-200 bg-stone-50 flex items-center justify-between gap-2">
<div class="flex items-center gap-1.5 min-w-0">
<span class="material-symbols-outlined text-red-800 text-[18px]">picture_as_pdf</span>
<span class="text-xs text-stone-700 truncate font-medium">{{ basename($edit->pdf_path) }}</span>
</div>
<div class="flex items-center gap-2 shrink-0">
<a href="{{ route('epaper.show', $edit->id) }}" target="_blank" class="text-xs text-red-800 font-semibold hover:underline inline-flex items-center gap-0.5">
<span class="material-symbols-outlined text-[14px]">visibility</span>
<span>देखें</span>
</a>
<a href="{{ asset($edit->pdf_path) }}" download class="text-xs text-stone-600 hover:underline">
<span>डाउनलोड</span>
</a>
</div>
</div>
@endif
<input type="file" name="pdf" accept="application/pdf" data-max-kb="102400" data-label="PDF फ़ाइल · PDF file"/>
@error('pdf')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div><label class="block text-xs font-semibold mb-1">मुखपृष्ठ चित्र (वैकल्पिक) · Cover image (optional)</label>
@if($edit && $edit->cover_image)<img src="{{ asset($edit->cover_image) }}" class="w-16 h-20 object-cover rounded mb-1" alt=""/>@endif
<input type="file" name="cover_image" accept="image/*" data-max-kb="10240" data-label="मुखपृष्ठ चित्र · Cover image"/></div>
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $edit->is_active ?? 1) ? 'checked' : '' }}/> सक्रिय · Active</label>
<div class="flex gap-2">
<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded font-semibold" type="submit">सहेजें · Save</button>
@if($edit)<a href="{{ route('admin.epapers') }}" class="px-4 py-2 rounded border">रद्द करें · Cancel</a>@endif
</div>
</form>
</div>
</div>
@endsection
