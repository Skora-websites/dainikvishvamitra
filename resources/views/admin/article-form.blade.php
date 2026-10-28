@extends('layouts.admin')

@section('content')
@php
$isNew = empty($art) || empty($art->id);
$pubVal = old('published_at', ($art && $art->published_at) ? $art->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
$kpText = old('key_points', $kp_text ?? '');
@endphp
<form method="post" enctype="multipart/form-data" action="{{ $isNew ? route('admin.articles.store') : route('admin.articles.update', $art->id) }}" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
@csrf
<div class="lg:col-span-2 space-y-4">
<div class="bg-white rounded-lg border p-4 space-y-3">
<div>
<label class="block text-sm font-semibold mb-1">Title *</label>
<input name="title" required value="{{ old('title', $art->title ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
<label class="block text-sm font-semibold mb-1">Slug <span class="font-normal text-stone-500">(leave blank to generate from title)</span></label>
<input name="slug" value="{{ old('slug', $art->slug ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('slug')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
<label class="block text-sm font-semibold mb-1">Language</label>
@php($lang = old('language', $art->language ?? 'hi'))
<select name="language" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
<option value="hi" {{ $lang === 'hi' ? 'selected' : '' }}>Hindi</option>
<option value="en" {{ $lang === 'en' ? 'selected' : '' }}>English</option>
</select>
@error('language')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
<label class="block text-sm font-semibold mb-1">Excerpt</label>
<textarea name="excerpt" rows="2" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">{{ old('excerpt', $art->excerpt ?? '') }}</textarea>
@error('excerpt')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
<label class="block text-sm font-semibold mb-1">Body</label>
<textarea name="body" id="article-body" rows="12">{{ old('body', $art->body ?? '') }}</textarea>
@error('body')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
<label class="block text-sm font-semibold mb-1">Key points <span class="font-normal text-stone-500">(one per line — &lt;strong&gt; allowed)</span></label>
<textarea name="key_points" id="article-key-points" rows="4">{{ $kpText }}</textarea>
@error('key_points')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
</div>
<div class="bg-white rounded-lg border p-4 space-y-3">
<h3 class="font-bold">Image</h3>
@if(!empty($art) && !empty($art->image))
<img src="{{ $art->image_url }}" class="w-full max-h-48 object-cover rounded border" alt=""/>
<label class="flex items-center gap-1.5 text-xs"><input type="checkbox" name="remove_image" value="1" {{ old('remove_image') ? 'checked' : '' }}/> Remove</label>
@endif
<input type="file" name="image" accept="image/*" data-max-kb="10240" data-label="Image" class="text-sm"/>
@error('image')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
<div class="grid grid-cols-2 gap-2">
<div>
<label class="block text-xs font-semibold mb-1">Alt text</label>
<input name="image_alt" value="{{ old('image_alt', $art->image_alt ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Caption</label>
<input name="image_caption" value="{{ old('image_caption', $art->image_caption ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
</div>
</div>
<div class="bg-white rounded-lg border p-4 space-y-3">
<h3 class="font-bold">SEO (optional)</h3>
<input name="meta_title" value="{{ old('meta_title', $art->meta_title ?? '') }}" placeholder="Meta title" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
<input name="meta_desc" value="{{ old('meta_desc', $art->meta_desc ?? '') }}" placeholder="Meta description" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('meta_title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
@error('meta_desc')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
</div>
<div class="space-y-4">
<div class="bg-white rounded-lg border p-4 space-y-3">
<h3 class="font-bold">Publish</h3>
<div>
<label class="block text-xs font-semibold mb-1">Category *</label>
<select name="category_id" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
@php($selCat = (int) old('category_id', $art->category_id ?? 0))
@foreach(($cats ?? []) as $c)<option value="{{ (int) $c->id }}" {{ $selCat === (int) $c->id ? 'selected' : '' }}>{{ $c->name_hi }}</option>@endforeach
</select>
@error('category_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
<label class="block text-xs font-semibold mb-1">Status</label>
<select name="status" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
<option value="draft" {{ old('status', $art->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
<option value="published" {{ old('status', $art->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
</select>
@error('status')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
<label class="block text-xs font-semibold mb-1">Published at</label>
<input type="datetime-local" name="published_at" value="{{ $pubVal }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error('published_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div class="grid grid-cols-2 gap-2 text-sm">
@php($chk = fn($f) => old($f, !empty($art) && $art->$f) ? 'checked' : '')
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_breaking" {{ $chk('is_breaking') }}/> Breaking</label>
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_featured" {{ $chk('is_featured') }}/> Featured</label>
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_opinion" {{ $chk('is_opinion') }}/> Opinion</label>
<label class="flex items-center gap-1.5"><input type="checkbox" name="is_visual" {{ $chk('is_visual') }}/> Visual</label>
</div>
<div class="grid grid-cols-2 gap-2">
<div>
<label class="block text-xs font-semibold mb-1">Reading (min)</label>
<input type="number" min="1" name="read_minutes" value="{{ (int) old('read_minutes', $art->read_minutes ?? 4) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Comment count</label>
<input type="number" min="0" name="comment_count" value="{{ (int) old('comment_count', $art->comment_count ?? 0) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
</div>
</div>
<div class="bg-white rounded-lg border p-4 space-y-3">
<h3 class="font-bold">Details</h3>
<div>
<label class="block text-xs font-semibold mb-1">Badge label</label>
<input name="badge_label" value="{{ old('badge_label', $art->badge_label ?? '') }}" placeholder="e.g. Special coverage" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Badge color</label>
<select name="badge_color" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
@php($badgeColor = old('badge_color', $art->badge_color ?? 'bg-primary'))
@foreach(['bg-primary' => 'Red', 'bg-secondary' => 'Blue', 'bg-tertiary' => 'Slate', 'bg-breaking-red' => 'Breaking red', 'bg-masthead-crimson' => 'Maroon'] as $bc => $bl)
<option value="{{ $bc }}" {{ $badgeColor === $bc ? 'selected' : '' }}>{{ $bl }}</option>
@endforeach
</select>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Stat <span class="font-normal text-stone-500">(for market cards)</span></label>
<input name="stat_text" value="{{ old('stat_text', $art->stat_text ?? '') }}" placeholder="e.g. BDL +4.8%" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
<div class="grid grid-cols-2 gap-2">
<div>
<label class="block text-xs font-semibold mb-1">Home slot</label>
@php($homeSlot = (string) old('home_slot', $art->home_slot ?? ''))
@php($hslots = ['' => 'None', 'hero' => 'Hero', 'spot' => 'Spotlight', 'market' => 'Market card', 'pol_lead' => 'Politics lead', 'pol_list' => 'Politics list', 'sport_lead' => 'Sports lead', 'sport_list' => 'Sports list', 'ent' => 'Entertainment', 'real' => 'Real estate'])
<select name="home_slot" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
@foreach($hslots as $hv => $hl)<option value="{{ $hv }}" {{ $homeSlot === (string) $hv ? 'selected' : '' }}>{{ $hl }}</option>@endforeach
</select>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Home order</label>
<input type="number" name="home_order" value="{{ (int) old('home_order', $art->home_order ?? 0) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
</div>
<div class="grid grid-cols-2 gap-2">
<div>
<label class="block text-xs font-semibold mb-1">Category slot</label>
@php($catSlot = (string) old('cat_slot', $art->cat_slot ?? ''))
@php($cslots = ['' => 'None', 'lead' => 'Lead package', 'grid' => 'Grid card', 'dossier' => 'Dossier list'])
<select name="cat_slot" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800">
@foreach($cslots as $cv => $cl)<option value="{{ $cv }}" {{ $catSlot === (string) $cv ? 'selected' : '' }}>{{ $cl }}</option>@endforeach
</select>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Category order</label>
<input type="number" name="cat_order" value="{{ (int) old('cat_order', $art->cat_order ?? 0) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Time label <span class="font-normal text-stone-500">(e.g. 40 min ago — blank = automatic)</span></label>
<input name="time_label" value="{{ old('time_label', $art->time_label ?? '') }}" placeholder="40 min ago" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Location</label>
<input name="location" value="{{ old('location', $art->location ?? '') }}" placeholder="e.g. Kolkata" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Author</label>
<input name="author_name" value="{{ old('author_name', $art->author_name ?? 'Special correspondent bureau') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Author role</label>
<input name="author_role" value="{{ old('author_role', $art->author_role ?? '') }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
</div>
<div>
<label class="block text-xs font-semibold mb-1">Author photo</label>
@if(!empty($art) && $art->author_image_url)<img src="{{ $art->author_image_url }}" class="w-12 h-12 rounded-full object-cover mb-1" alt=""/>@endif
<input type="file" name="author_image" accept="image/*" data-max-kb="10240" data-label="Author photo" class="text-sm"/>
@error('author_image')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
</div>
<button class="w-full bg-red-800 hover:bg-red-900 text-white py-2.5 rounded font-semibold" type="submit">Save</button>
@if(!$isNew)<a target="_blank" class="block text-center w-full bg-white border py-2 rounded text-sm" href="{{ url('article/' . $art->slug) }}">View</a>@endif
</div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/editor.js') }}?v={{ filemtime(public_path('js/editor.js')) }}"></script>
<script>
(function () {
  function init () {
    var bodyEl = document.getElementById('article-body');
    var kpEl   = document.getElementById('article-key-points');

    if (bodyEl && window.DvEditor) {
      DvEditor.create(bodyEl, {
        uploadUrl : '{{ route('admin.uploads.image') }}',
        csrfToken : '{{ csrf_token() }}',
        language  : '{{ old('language', $art->language ?? 'hi') }}',
        placeholder: 'लेख का मुख्य भाग यहाँ लिखें…',
      });
    }

    if (kpEl && window.DvEditor) {
      DvEditor.createLite(kpEl, {
        language   : '{{ old('language', $art->language ?? 'hi') }}',
        placeholder: 'मुख्य बिंदु यहाँ लिखें…',
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
