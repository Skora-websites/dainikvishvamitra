{{--
    XML sitemap: static routes + categories + published articles + media + pages.
    Ported from legacy sitemap.php. NOT a Blade @extends page: it is a raw XML
    document, matching the legacy `header('Content-Type: application/xml')`.
    Gallery and podcasts were removed from the migration, so their legacy
    sitemap entries are intentionally dropped.
--}}
@php
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\Video;

$cats = Category::where('status', 'active')->orderBy('sort_order')->orderBy('id')->get(['slug']);
$arts = Article::published()
    ->where('published_at', '<=', now())
    ->orderByDesc('published_at')
    ->limit(1000)
    ->get(['slug', 'published_at']);
$vids = Video::where('is_active', 1)->orderBy('sort_order')->orderBy('id')->limit(200)->get(['id']);
$pgs = Page::where('status', 'published')->orderBy('id')->get(['slug']);

$static = ['video', 'archive', 'breaking', 'opinion', 'horoscope', 'epaper', 'contact', 'subscribe', 'search'];
$today = date('Y-m-d');
@endphp
<?php echo '<?xml version="1.0" encoding="UTF-8"?>', "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($static as $s)
<url><loc>{{ e(url($s)) }}</loc><changefreq>daily</changefreq><priority>0.8</priority></url>
@endforeach
@foreach ($cats as $c)
<url><loc>{{ e(url('category/' . $c->slug)) }}</loc><changefreq>hourly</changefreq><priority>0.9</priority></url>
@endforeach
@foreach ($arts as $a)
<url><loc>{{ e(url('article/' . $a->slug)) }}</loc>@if (!empty($a->published_at))<lastmod>{{ e($a->published_at->format('Y-m-d')) }}</lastmod>@endif<changefreq>weekly</changefreq><priority>0.7</priority></url>
@endforeach
@foreach ($vids as $v)
<url><loc>{{ e(url('video?play=' . (int) $v->id)) }}</loc><changefreq>weekly</changefreq><priority>0.6</priority></url>
@endforeach
@foreach ($pgs as $p)
<url><loc>{{ e(url('page/' . $p->slug)) }}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
@endforeach
</urlset>
