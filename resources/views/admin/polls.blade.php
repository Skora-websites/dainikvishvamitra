@extends('layouts.admin')

@section('content')
@php($rows = $rows ?? collect())
@php($edit = ($edit ?? null) ?? $rows->firstWhere('id', (int) request('edit')))
@php($polls = $rows)
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 space-y-4">
@foreach($polls as $p)
@php($total = (int) $p->options->sum('votes'))
<div class="bg-white rounded-lg border p-4">
<div class="flex items-start justify-between gap-3">
<div>
<span class="text-xs px-2 py-0.5 rounded {{ $p->context === 'home' ? 'bg-stone-200' : 'bg-red-100 text-red-800' }}">{{ $p->context === 'home' ? 'होम · Home' : 'श्रेणी · Category' }}</span>
<span class="text-xs {{ $p->is_active ? 'text-green-700' : 'text-stone-400' }}">{{ $p->is_active ? '● सक्रिय · Active' : '○ बंद · Inactive' }}</span>
<div class="font-bold mt-1">{{ $p->question }}</div>
<div class="text-xs text-stone-500">कुल मत · Total votes: {{ \App\Support\Site::hiDigits(number_format($total)) }}</div>
</div>
<div class="flex gap-2 shrink-0 text-xs">
<a class="text-red-800 hover:underline" href="{{ route('admin.polls', ['edit' => $p->id]) }}">संपादित करें · Edit</a>
<form method="post" action="{{ route('admin.polls.reset', $p->id) }}" class="inline" onsubmit="return confirm('मत शून्य करें? / Reset votes?')">@csrf<button class="text-stone-600 hover:underline" type="submit">रीसेट · Reset</button></form>
<form method="post" action="{{ route('admin.polls.delete', $p->id) }}" class="inline" onsubmit="return confirm('पोल हटाएं? / Delete poll?')">@csrf<button class="text-red-600 hover:underline" type="submit">हटाएं · Delete</button></form>
</div>
</div>
<div class="mt-2 space-y-1.5">
@foreach($p->options as $o)
@php($pct = $total > 0 ? (int) round($o->votes * 100 / $total) : 0)
<div class="flex items-center gap-2 text-xs">
<span class="w-48 truncate">{{ $o->option_text }}</span>
<div class="flex-1 bg-stone-200 h-2 rounded-full overflow-hidden"><div class="{{ $o->color_class ?: 'bg-primary' }} h-full" style="width: {{ $pct }}%"></div></div>
<span class="w-24 text-right font-semibold">{{ \App\Support\Site::hiDigits((string) $pct) }}% ({{ \App\Support\Site::hiDigits(number_format((int) $o->votes)) }})</span>
<form method="post" action="{{ route('admin.polls.delete_option', $o->id) }}" class="inline" onsubmit="return confirm('विकल्प हटाएं? / Delete option?')">@csrf<button class="text-red-500 hover:underline" type="submit">✕ · Delete</button></form>
</div>
@endforeach
</div>
<form method="post" action="{{ route('admin.polls.add_option') }}" class="mt-2 flex gap-2 text-sm">
@csrf
<input type="hidden" name="poll_id" value="{{ $p->id }}"/>
<input name="option_text" placeholder="नया विकल्प जोड़ें... · Add new option..." class="flex-1 px-3 py-1.5 border rounded focus:outline-none focus:border-red-800"/>
<button class="bg-stone-800 text-white px-3 py-1.5 rounded" type="submit">जोड़ें · Add</button>
</form>
</div>
@endforeach
@if($polls->isEmpty())<div class="bg-white rounded-lg border p-4 text-center text-stone-500">कोई पोल नहीं। · No polls.</div>@endif
</div>
<div class="bg-white rounded-lg border p-4 h-fit">
<h2 class="font-bold mb-3">{{ $edit ? 'पोल संपादित करें · Edit poll' : 'नया पोल · New poll' }}</h2>
<form method="post" action="{{ $edit ? route('admin.polls.update', $edit->id) : route('admin.polls.store') }}" class="space-y-3 text-sm">
@csrf
<input type="hidden" name="id" value="{{ (int) ($edit->id ?? 0) }}"/>
<div><label class="block text-xs font-semibold mb-1">प्रश्न · Question *</label><textarea name="question" rows="2" required class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">{{ old('question', $edit->question ?? '') }}</textarea>
@error('question')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">स्थान · Placement</label>
<select name="context" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
<option value="home" {{ old('context', $edit->context ?? 'home') !== 'category' ? 'selected' : '' }}>होम पेज · Home page</option>
<option value="category" {{ old('context', $edit->context ?? '') === 'category' ? 'selected' : '' }}>श्रेणी पेज · Category page</option>
</select>
@error('context')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
@if($edit)
<p class="text-xs text-stone-500">विकल्प बदलने हेतु नीचे "नया विकल्प जोड़ें" व ✕ बटन से प्रबंधित करें। · Add/remove options below with the "Add new option" field and the ✕ button.</p>
@else
<div><label class="block text-xs font-semibold mb-1">विकल्प · Options <span class="font-normal text-stone-500">(प्रति पंक्ति एक · one per line)</span></label>
<textarea name="options" rows="4" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800" placeholder="विकल्प १&#10;विकल्प २&#10;विकल्प ३">{{ old('options') }}</textarea>
@error('options')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
@endif
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $edit->is_active ?? 1) ? 'checked' : '' }}/> सक्रिय · Active</label>
<div class="flex gap-2">
<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded font-semibold" type="submit">सहेजें · Save</button>
@if($edit)<a href="{{ route('admin.polls') }}" class="px-4 py-2 rounded border">रद्द करें · Cancel</a>@endif
</div>
</form>
</div>
</div>
@endsection
