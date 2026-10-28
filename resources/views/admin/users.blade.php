@extends('layouts.admin')

@section('content')
@php($rows = $rows ?? collect())
@php($me = $me ?? ($user ?? null))
@php($edit = ($edit ?? null) ?? $rows->firstWhere('id', (int) request('edit')))
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 bg-white rounded-lg border divide-y text-sm">
@foreach(($rows ?? []) as $r)
<div class="px-4 py-2.5 flex items-center justify-between gap-3">
<div class="min-w-0">
<div class="font-medium">{{ $r->name }} <span class="text-xs px-2 py-0.5 rounded {{ $r->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-stone-200 text-stone-700' }}">{{ $r->role }}</span></div>
<div class="text-xs text-stone-500">{{ $r->username }} · {{ $r->email }} · {{ $r->is_active ? 'सक्रिय · Active' : 'बंद · Inactive' }}</div>
</div>
<div class="flex gap-2 shrink-0 text-xs">
<a class="text-red-800 hover:underline" href="{{ route('admin.users', ['edit' => $r->id]) }}">संपादित करें · Edit</a>
@if(!$me || (int) $r->id !== (int) $me->id)
<form method="post" action="{{ route('admin.users.delete', $r->id) }}" class="inline" onsubmit="return confirm('हटाएं? / Delete?')">@csrf<button class="text-red-600 hover:underline" type="submit">हटाएं · Delete</button></form>
@endif
</div>
</div>
@endforeach
</div>
<div class="bg-white rounded-lg border p-4 h-fit">
<h2 class="font-bold mb-3">{{ $edit ? 'संपादित करें · Edit user' : 'नया उपयोगकर्ता · New user' }}</h2>
<form method="post" action="{{ $edit ? route('admin.users.update', $edit->id) : route('admin.users.store') }}" class="space-y-3 text-sm">
@csrf
<input type="hidden" name="id" value="{{ (int) ($edit->id ?? 0) }}"/>
<div><label class="block text-xs font-semibold mb-1">नाम · Name *</label><input name="name" required value="{{ old('name', $edit->name ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">उपयोगकर्ता नाम · Username *</label><input name="username" required value="{{ old('username', $edit->username ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('username')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">ईमेल · Email *</label><input name="email" type="email" required value="{{ old('email', $edit->email ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">पासवर्ड {{ $edit ? '(बदलने हेतु भरें) · Password (fill to change)' : '· Password *' }}</label><input name="password" type="password" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">पासवर्ड पुष्टि · Confirm {{ $edit ? '' : '*' }}</label><input name="password_confirmation" type="password" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/></div>
<div><label class="block text-xs font-semibold mb-1">भूमिका · Role</label>
<select name="role" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
<option value="editor" {{ old('role', $edit->role ?? '') !== 'admin' ? 'selected' : '' }}>संपादक (Editor)</option>
<option value="admin" {{ old('role', $edit->role ?? '') === 'admin' ? 'selected' : '' }}>प्रशासक (Admin)</option>
</select></div>
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $edit->is_active ?? 1) ? 'checked' : '' }}/> सक्रिय · Active</label>
<div class="flex gap-2">
<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded font-semibold" type="submit">सहेजें · Save</button>
@if($edit)<a href="{{ route('admin.users') }}" class="px-4 py-2 rounded border">रद्द करें · Cancel</a>@endif
</div>
</form>
</div>
</div>
@endsection
