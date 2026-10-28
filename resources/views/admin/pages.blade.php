@extends('layouts.admin')

@section('content')
@php($rows = $rows ?? collect())
@php($edit = ($edit ?? null) ?? $rows->firstWhere('slug', (string) request('slug')))
@php($pageSlug = $edit->slug ?? '')
@php($pageTitle = $edit->title ?? '')
@php($pageBody = $edit->body ?? '')
@php($pageStatus = $edit->status ?? '')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
<div class="bg-white rounded-lg border divide-y text-sm h-fit">
@foreach($rows as $r)
<div class="px-4 py-2.5 flex items-center justify-between gap-3">
<div class="min-w-0">
<div class="font-medium">{{ $r->title }}</div>
<div class="text-xs text-stone-500">page.php?slug={{ $r->slug }} · {{ $r->status === 'published' ? 'Published' : 'Draft' }}</div>
</div>
<div class="flex gap-2 shrink-0 text-xs">
<a class="text-stone-600 hover:underline" target="_blank" href="{{ url('page/' . $r->slug) }}">View</a>
<a class="text-red-800 hover:underline" href="{{ route('admin.pages', ['slug' => $r->slug]) }}">Edit</a>
<form method="post" action="{{ route('admin.pages.delete', $r->slug) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf<button class="text-red-600 hover:underline" type="submit">Delete</button></form>
</div>
</div>
@endforeach
@if($rows->isEmpty())<div class="px-4 py-6 text-center text-stone-500">No pages.</div>@endif
</div>
<div class="lg:col-span-2 bg-white rounded-lg border p-4 h-fit">
<h2 class="font-bold mb-3">{{ $edit ? 'Edit page' : 'New page' }}</h2>
<form method="post" action="{{ $edit ? route('admin.pages.update', $pageSlug) : route('admin.pages.store') }}" class="space-y-3 text-sm">
@csrf
<div class="grid grid-cols-2 gap-2">
<div><label class="block text-xs font-semibold mb-1">Title *</label><input name="title" required value="{{ old('title', $pageTitle) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-xs font-semibold mb-1">Slug</label><input name="slug" value="{{ old('slug', $pageSlug) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('slug')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
</div>
<div><label class="block text-xs font-semibold mb-1">Body</label><textarea name="body" id="page-body" rows="10">{{ old('body', $pageBody) }}</textarea>
@error('body')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div><label class="block text-xs font-semibold mb-1">Status</label>
<select name="status" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
<option value="published" {{ old('status', $pageStatus) !== 'draft' ? 'selected' : '' }}>Published</option>
<option value="draft" {{ old('status', $pageStatus) === 'draft' ? 'selected' : '' }}>Draft</option>
</select></div>
<div class="flex gap-2">
<button class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded font-semibold" type="submit">Save</button>
@if($edit)<a href="{{ route('admin.pages') }}" class="px-4 py-2 rounded border">Cancel</a>@endif
</div>
</form>
</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/editor.js') }}?v={{ filemtime(public_path('js/editor.js')) }}"></script>
<script>
(function () {
  function init () {
    var el = document.getElementById('page-body');
    if (el && window.DvEditor) {
      DvEditor.create(el, {
        uploadUrl : '{{ route('admin.uploads.image') }}',
        csrfToken : '{{ csrf_token() }}',
        language  : 'hi',
        placeholder: 'पेज का विषय यहाँ लिखें…',
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>
@endpush
