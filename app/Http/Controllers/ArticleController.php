<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __invoke(string $slug): View
    {
        $art = Article::with(['category', 'keyPoints'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (! $art) {
            abort(404, 'लेख नहीं मिला।');
        }

        // Count the view once per session per article.
        $viewKey = 'viewed_article_'.$art->id;
        if (! session()->has($viewKey)) {
            Article::where('id', $art->id)->increment('views');
            session()->put($viewKey, true);
            $art->views = (int) $art->views + 1;
        }

        // Related: same category, newest first, excluding this article.
        $related = Article::published()
            ->where('category_id', $art->category_id)
            ->where('id', '!=', $art->id)
            ->newest()
            ->limit(4)
            ->get();

        return view('public.article', [
            'nav_active' => optional($art->category)->slug,
            'page_title' => $art->meta_title_resolved.' | दैनिक विश्वामित्र',
            'page_desc' => $art->meta_desc_resolved,
            // Social share thumbnail. Only the article's own image, never the
            // shared placeholder (it depicts another story).
            'page_image' => ! empty($art->image) ? $art->image_url : null,
            'page_image_alt' => $art->image_alt ?: $art->meta_title_resolved,
            'art' => $art,
            'key_points' => $art->keyPoints,
            'related' => $related,
        ]);
    }
}
