@extends('layouts.admin')

@section('content')
@php($rows = $rows ?? collect())
@php($edit = ($edit ?? null) ?? $rows->firstWhere('id', (int) request('edit')))
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 bg-white rounded-lg border divide-y text-sm">
@foreach($rows as $r)
<div class="px-4 py-2.5 flex items-center justify-between gap-3">
<div><span class="font-semibold">{{ $r->state_name }}</span> <span class="text-stone-500">· {{ $r->date_text }}</span> <span class="text-xs {{ $r->is_active ? 'text-green-700' : 'text-stone-400' }}">{{ $r->is_active ? '● सक्रिय · Active' : '○ बंद · Inactive' }}</span></div>
<div class="flex gap-2 shrink-0 text-xs">
<a class="text-red-800 hover:underline" href="{{ route('admin.elections', ['edit' => $r->id]) }}">संपादित करें · Edit election</a>
<form method="post" action="{{ route('admin.elections.delete', $r->id) }}" class="inline" onsubmit="return confirm('हटाएं? / Delete?')">@csrf<button class="text-red-600 hover:underline" type="submit">हटाएं · Delete</button></form>
</div>
</div>
@endforeach
@if($rows->isEmpty())<div class="px-4 py-6 text-center text-stone-500">कोई चुनाव कार्यक्रम नहीं। · No election events.</div>@endif
</div>
<div class="bg-white rounded-lg border p-4 h-fit">
<h2 class="font-bold mb-3">{{ $edit ? 'संपादित करें · Edit election' : 'नया चुनाव · New election' }}</h2>
<form method="post" action="{{ $edit ? route('admin.elections.update', $edit->id) : route('admin.elections.store') }}" class="space-y-3 text-sm">
@csrf
<input type="hidden" name="id" value="{{ (int) ($edit->id ?? 0) }}"/>
<div><label class="block text-xs font-semibold mb-1">राज्य · State *</label><input name="state_name" required value="{{ old('state_name', $edit->state_name ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('state_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">तिथि (पाठ) · Date (text) *</label><input name="date_text" required value="{{ old('date_text', $edit->date_text ?? '') }}" placeholder="जैसे: अप्रैल २०२६ · e.g. April 2026" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('date_text')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">क्रम · Order</label><input type="number" name="sort_order" value="{{ (int) old('sort_order', $edit->sort_order ?? 0) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $edit->is_active ?? 1) ? 'checked' : '' }}/> सक्रिय · Active</label>
<div class="flex gap-2">
<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded font-semibold" type="submit">सहेजें · Save</button>
@if($edit)<a href="{{ route('admin.elections') }}" class="px-4 py-2 rounded border">रद्द करें · Cancel</a>@endif
</div>
</form>
</div>
</div>
@endsection
