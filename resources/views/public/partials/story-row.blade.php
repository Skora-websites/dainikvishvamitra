{{--
    Search / author / archive story row. Ported verbatim from the legacy
    `foreach ($results as $r)` / `foreach ($items as $r)` blocks in search.php,
    author.php and archive.php (identical markup in all three).

    Mirrors legacy story_time(): fixed admin label wins, else relative time,
    via Site::timeAgo() which ports legacy hindi_time_ago() exactly.

    @param  \App\Models\Article  $r
--}}
@php
    // mirrors legacy story_time(): fixed admin label wins, else relative time.
    $__st = trim((string) $r->time_label);
    if ($__st === '' && $r->published_at) {
        $__st = \App\Support\Site::timeAgo($r->published_at);
    }
@endphp
<article class="bg-surface-container-lowest p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row gap-4 items-center cursor-pointer" onclick="location.href='{{ url('article/' . $r->slug) }}'">
@if (!empty($r->image))
<div class="w-full sm:w-44 aspect-[4/3] rounded overflow-hidden shrink-0"><img class="w-full h-full object-cover" alt="{{ e($r->image_alt ?: $r->title) }}" src="{{ $r->image_url }}" loading="lazy"/></div>
@endif
<div class="flex-1 min-w-0">
<div class="flex items-center gap-2 font-caption text-xs text-ink-muted mb-1"><span class="text-primary font-bold">{{ e($r->category ? $r->category->name_hi : '') }}</span><span>•</span><span>{{ e($__st) }}</span></div>
<h3 class="font-headline-sm text-headline-sm text-ink-primary hover:text-primary transition-colors leading-snug">{{ e($r->title) }}</h3>
<p class="font-body-sm text-body-sm text-ink-secondary mt-1.5 line-clamp-2">{{ e($r->excerpt ?: \App\Support\Site::excerpt($r->body ?? '', 140)) }}</p>
</div>
</article>
