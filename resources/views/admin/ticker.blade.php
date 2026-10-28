@extends('layouts.admin')

@section('content')
@php($rows = collect(($ribbon ?? collect())->all())->merge(($flash ?? collect())->all()))
@php($edit = ($edit ?? null) ?? $rows->firstWhere('id', (int) request('edit')))
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 space-y-4">
@foreach([['flash', $flash, 'फ़्लैश पट्टी · Flash strip'], ['ribbon', $ribbon, 'ब्रेकिंग रिबन · Breaking ribbon']] as [$slotName, $slotRows, $slotLabel])
<div>
<h2 class="font-bold text-sm mb-2 text-stone-600">{{ $slotLabel }}</h2>
<div class="bg-white rounded-lg border divide-y text-sm">
@foreach(($slotRows ?? []) as $r)
<div class="px-4 py-2.5 flex items-center justify-between gap-3">
<div class="min-w-0">
<span class="text-xs px-2 py-0.5 rounded {{ $r->slot === 'ribbon' ? 'bg-red-100 text-red-800' : 'bg-stone-200 text-stone-700' }}">{{ $r->slot === 'ribbon' ? 'ब्रेकिंग रिबन · Breaking ribbon' : 'फ़्लैश पट्टी · Flash strip' }}</span>
<span class="text-xs {{ $r->is_active ? 'text-green-700' : 'text-stone-400' }}">{{ $r->is_active ? '● सक्रिय · Active' : '○ बंद · Inactive' }}</span>
<div class="truncate mt-0.5">{{ $r->text }}</div>
</div>
<div class="flex gap-2 shrink-0 text-xs">
<a class="text-red-800 hover:underline" href="{{ route('admin.ticker', ['edit' => $r->id]) }}">संपादित करें · Edit</a>
<form method="post" action="{{ route('admin.ticker.delete', $r->id) }}" class="inline" onsubmit="return confirm('हटाएं? / Delete?')">@csrf<button class="text-red-600 hover:underline" type="submit">हटाएं · Delete</button></form>
</div>
</div>
@endforeach
@if(($slotRows ?? collect())->isEmpty())<div class="px-4 py-6 text-center text-stone-500">कोई टिकर नहीं। · No ticker items.</div>@endif
</div>
</div>
@endforeach
</div>
<div class="bg-white rounded-lg border p-4 h-fit">
<h2 class="font-bold mb-3">{{ $edit ? 'संपादित करें · Edit' : 'नया टिकर · New ticker' }}</h2>
<form method="post" action="{{ $edit ? route('admin.ticker.update', $edit->id) : route('admin.ticker.store') }}" class="space-y-3 text-sm">
@csrf
<input type="hidden" name="id" value="{{ (int) ($edit->id ?? 0) }}"/>
<div><label class="block text-xs font-semibold mb-1">स्थान · Slot</label>
<select name="slot" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
<option value="flash" {{ old('slot', $edit->slot ?? '') !== 'ribbon' ? 'selected' : '' }}>फ़्लैश पट्टी (होम शीर्ष) · Flash strip (home top)</option>
<option value="ribbon" {{ old('slot', $edit->slot ?? '') === 'ribbon' ? 'selected' : '' }}>ब्रेकिंग रिबन (हेडर नीचे) · Breaking ribbon (below header)</option>
</select></div>
<div><label class="block text-xs font-semibold mb-1">पाठ · Text *</label><textarea name="text" rows="3" required class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">{{ old('text', $edit->text ?? '') }}</textarea>
@error('text')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">क्रम · Order</label><input type="number" name="sort_order" value="{{ (int) old('sort_order', $edit->sort_order ?? 0) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $edit->is_active ?? 1) ? 'checked' : '' }}/> सक्रिय · Active</label>
<div class="flex gap-2">
<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded font-semibold" type="submit">सहेजें · Save</button>
@if($edit)<a href="{{ route('admin.ticker') }}" class="px-4 py-2 rounded border">रद्द करें · Cancel</a>@endif
</div>
</form>
</div>
</div>
@endsection
