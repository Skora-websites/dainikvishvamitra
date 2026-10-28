@extends('layouts.admin')

@section('content')
@php($errMsg = $err ?? ($msg ?? null))
<div class="w-full max-w-sm bg-white rounded-xl shadow-sm border border-stone-200 p-8">
<div class="text-center mb-6">
<div class="font-bold text-2xl text-red-800 serif">दैनिक विश्वामित्र</div>
<div class="text-sm text-stone-500 mt-1">Admin panel — please log in</div>
</div>
@if($errMsg)<div class="mb-4 px-3 py-2 rounded text-sm bg-red-100 text-red-800">{{ $errMsg }}</div>@endif
@error('username')<div class="mb-4 px-3 py-2 rounded text-sm bg-red-100 text-red-800">{{ $message }}</div>@enderror
@error('password')<div class="mb-4 px-3 py-2 rounded text-sm bg-red-100 text-red-800">{{ $message }}</div>@enderror
<form method="post" action="{{ route('admin.login.attempt') }}" class="space-y-4">
@csrf
<div>
<label class="block text-sm font-semibold mb-1 text-stone-700">Username</label>
<input name="username" required autofocus value="{{ old('username') }}" class="w-full px-3 py-2 border border-stone-300 rounded focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800/20"/>
</div>
<div>
<label class="block text-sm font-semibold mb-1 text-stone-700">Password</label>
<input name="password" required type="password" class="w-full px-3 py-2 border border-stone-300 rounded focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800/20"/>
</div>
<button class="w-full bg-red-800 hover:bg-red-900 text-white py-2.5 rounded font-semibold transition-colors" type="submit">Log in</button>
</form>
<p class="mt-4 text-xs text-stone-500 text-center">Unauthorized access is prohibited.</p>
</div>
@endsection
