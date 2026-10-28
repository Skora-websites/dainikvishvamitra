{{--
    RSS 2.0 feed: latest 20 published articles.
    Ported from legacy rss.php. NOT a Blade @extends page: it is a raw XML
    document, matching the legacy `header('Content-Type: application/rss+xml')`.
--}}
@php
use App\Models\Article;

$items = Article::published()
    ->where('published_at', '<=', now())
    ->with('category')
    ->orderByDesc('published_at')
    ->limit(20)
    ->get(['id', 'title', 'slug', 'excerpt', 'body', 'published_at', 'author_name', 'category_id']);
@endphp
<?php echo '<?xml version="1.0" encoding="UTF-8"?>', "\n"; ?>
<rss version="2.0">
<channel>
<title>दैनिक विश्वामित्र — ताज़ा खबर</title>
<link>{{ e(url('/')) }}</link>
<description>भारत का प्रमुख एवं विश्वसनीय हिंदी दैनिक | स्थापना १९१६</description>
<language>hi-IN</language>
@foreach ($items as $it)
<item>
<title>{{ e($it->title) }}</title>
<link>{{ e(url('article/' . $it->slug)) }}</link>
<guid>{{ e(url('article/' . $it->slug)) }}</guid>
@if (!empty($it->published_at))<pubDate>{{ e($it->published_at->format(DATE_RSS)) }}</pubDate>@endif
<category>{{ e($it->category ? $it->category->name_hi : '') }}</category>
<description><![CDATA[{!! str_replace(']]>', ']]&gt;', \App\Support\Site::excerpt($it->excerpt ?: ($it->body ?? ''), 300)) !!}]]></description>
</item>
@endforeach
</channel>
</rss>
