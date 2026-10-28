@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg border">
<div class="flex items-center justify-between mb-3 px-4 pt-4">
<div class="text-sm text-stone-600">कुल · Total: <strong>{{ \App\Support\Site::hiDigits(number_format((int) ($total ?? \App\Models\Subscriber::count()))) }}</strong> पाठक · readers</div>
<a href="{{ route('admin.subscribers.export') }}" class="text-sm bg-stone-800 text-white px-3 py-1.5 rounded">CSV निर्यात · Export</a>
</div>
<div class="divide-y text-sm">
@foreach(($rows ?? []) as $r)
<div class="px-4 py-2.5 flex items-center justify-between gap-3">
<div class="min-w-0">
<div class="font-medium truncate">{{ $r->email }}</div>
<div class="text-xs text-stone-500">{{ $r->created_at }} · {{ $r->is_active ? 'सक्रिय · Active' : 'बंद · Inactive' }}</div>
</div>
<div class="flex gap-2 shrink-0 text-xs">
<form method="post" action="{{ route('admin.subscribers.toggle', $r->id) }}" class="inline">@csrf<button class="text-stone-600 hover:underline" type="submit">{{ $r->is_active ? 'बंद करें · Deactivate' : 'चालू करें · Activate' }}</button></form>
<form method="post" action="{{ route('admin.subscribers.delete', $r->id) }}" class="inline" onsubmit="return confirm('हटाएं? / Delete?')">@csrf<button class="text-red-600 hover:underline" type="submit">हटाएं · Delete</button></form>
</div>
</div>
@endforeach
@if(($rows ?? collect())->isEmpty())<div class="px-4 py-6 text-center text-stone-500">अभी कोई पाठक नहीं। · No subscribers yet.</div>@endif
</div>
@if(method_exists($rows ?? null, 'links'))
<div class="mt-3 px-4 pb-2">{{ $rows->links() }}</div>
@endif
</div>
@endsection
