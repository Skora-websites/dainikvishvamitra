@extends('layouts.admin')

@section('content')
@php($rows = $rows ?? collect())
@php($edit = ($edit ?? null) ?? $rows->firstWhere('id', (int) request('edit')))
<p class="text-sm text-stone-600 mb-4">होम पेज की सूचकांक पट्टी (सेंसेक्स/निफ्टी/सोना-चांदी)। बाज़ार समाचार कार्ड "व्यापार एवं बाजार" श्रेणी के लेख हैं — उन्हें <a class="text-red-800 underline" href="{{ route('admin.articles', ['cat' => 3]) }}">समाचार</a> में संपादित करें। · Manage the home page index strip (Sensex/Nifty/gold-silver) here; market news cards are articles — edit them under Articles.</p>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 bg-white rounded-lg border divide-y text-sm">
@foreach(($rows ?? []) as $r)
<div class="px-4 py-2.5 flex items-center justify-between gap-3">
<div>
<span class="font-semibold">{{ $r->label }}</span>
<span class="font-bold">{{ $r->value_text }}</span>
<span class="{{ $r->direction === 'up' ? 'text-green-700' : 'text-red-700' }} font-bold">{{ $r->change_text }}</span>
<span class="text-xs {{ $r->is_active ? 'text-green-700' : 'text-stone-400' }}">{{ $r->is_active ? '● सक्रिय · Active' : '○ बंद · Inactive' }}</span>
</div>
<div class="flex gap-2 shrink-0 text-xs">
<a class="text-red-800 hover:underline" href="{{ route('admin.market', ['edit' => $r->id]) }}">संपादित करें · Edit</a>
<form method="post" action="{{ route('admin.market.delete', $r->id) }}" class="inline" onsubmit="return confirm('हटाएं? / Delete?')">@csrf<button class="text-red-600 hover:underline" type="submit">हटाएं · Delete</button></form>
</div>
</div>
@endforeach
</div>
<div class="bg-white rounded-lg border p-4 h-fit">
<h2 class="font-bold mb-3">{{ $edit ? 'संपादित करें · Edit' : 'नया सूचकांक · New index' }}</h2>
<form method="post" action="{{ $edit ? route('admin.market.update', $edit->id) : route('admin.market.store') }}" class="space-y-3 text-sm">
@csrf
<input type="hidden" name="id" value="{{ (int) ($edit->id ?? 0) }}"/>
<div><label class="block text-xs font-semibold mb-1">लेबल · Label * <span class="font-normal text-stone-500">(जैसे: BSE सेंसेक्स: · e.g. BSE Sensex:)</span></label><input name="label" required value="{{ old('label', $edit->label ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('label')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div class="grid grid-cols-2 gap-2">
<div><label class="block text-xs font-semibold mb-1">मान · Value *</label><input name="value_text" required value="{{ old('value_text', $edit->value_text ?? '') }}" placeholder="८१,४६२.२० · e.g. 81,462.20" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('value_text')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">बदलाव · Change</label><input name="change_text" value="{{ old('change_text', $edit->change_text ?? '') }}" placeholder="▲ +२४५.१० · e.g. ▲ +245.10" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('change_text')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
</div>
<div><label class="block text-xs font-semibold mb-1">दिशा · Direction</label>
<select name="direction" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
<option value="up" {{ old('direction', $edit->direction ?? '') !== 'down' ? 'selected' : '' }}>तेज़ी (हरा ▲) · Up (green ▲)</option>
<option value="down" {{ old('direction', $edit->direction ?? '') === 'down' ? 'selected' : '' }}>गिरावट (लाल ▼) · Down (red ▼)</option>
</select>
@error('direction')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">क्रम · Order</label><input type="number" name="sort_order" value="{{ (int) old('sort_order', $edit->sort_order ?? 0) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $edit->is_active ?? 1) ? 'checked' : '' }}/> सक्रिय · Active</label>
<div class="flex gap-2">
<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded font-semibold" type="submit">सहेजें · Save</button>
@if($edit)<a href="{{ route('admin.market') }}" class="px-4 py-2 rounded border">रद्द करें · Cancel</a>@endif
</div>
</form>
</div>
</div>
@endsection
