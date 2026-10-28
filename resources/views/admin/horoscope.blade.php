@extends('layouts.admin')

@section('content')
@php($signs = collect($rows ?? [])->keyBy('id'))
<p class="text-sm text-stone-600 mb-4">12 राशियों की भविष्यवाणी, शुभ रंग/अंक व आराध्य — होम पेज व ज्योतिष पेज पर स्वतः दिखेगा। · Predictions, lucky colour/number and deity for the 12 signs — shown automatically on the home and horoscope pages.</p>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
@foreach($signs as $id => $r)
<div class="bg-white rounded-lg border p-4">
<form method="post" action="{{ route('admin.horoscope.update', $id) }}">
@csrf
<input type="hidden" name="name_hi" value="{{ $r->name_hi }}"/>
<input type="hidden" name="name_en" value="{{ $r->name_en }}"/>
<input type="hidden" name="symbol" value="{{ $r->symbol }}"/>
<input type="hidden" name="sort_order" value="{{ (int) $r->sort_order }}"/>
<div class="flex items-center gap-2 mb-2">
<span class="text-2xl">{{ $r->symbol }}</span>
<strong>{{ $r->name_hi }} ({{ $r->name_en }})</strong>
</div>
<textarea name="prediction" rows="3" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">{{ old('prediction', $r->prediction) }}</textarea>
@error('prediction')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
<div class="grid grid-cols-3 gap-2 mt-2 text-sm">
<div><label class="block text-xs font-semibold mb-1">शुभ रंग · Lucky colour</label><input name="lucky_color" value="{{ old('lucky_color', $r->lucky_color ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<div><label class="block text-xs font-semibold mb-1">शुभ अंक · Lucky number</label><input name="lucky_number" value="{{ old('lucky_number', $r->lucky_number ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<div><label class="block text-xs font-semibold mb-1">आराध्य · Deity</label><input name="deity" value="{{ old('deity', $r->deity ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
</div>
<div class="mt-2 text-right">
<button class="bg-stone-800 text-white px-3 py-1.5 rounded text-xs" type="submit">सहेजें · Save</button>
</div>
</form>
</div>
@endforeach
</div>
@endsection
