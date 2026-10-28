<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $articles = Article::published()->orderByDesc('id')->get(['slug', 'updated_at']);
        $pages = Page::where('status', 'published')->get(['slug', 'updated_at']);

        $urls = [];

        $urls[] = ['loc' => url('/'), 'lastmod' => now()->toAtomString(), 'priority' => '1.0'];
        $urls[] = ['loc' => url('category/latest-news'), 'priority' => '0.9'];
        $urls[] = ['loc' => url('breaking'), 'priority' => '0.9'];
        $urls[] = ['loc' => url('opinion'), 'priority' => '0.8'];
        $urls[] = ['loc' => url('video'), 'priority' => '0.8'];
        $urls[] = ['loc' => url('horoscope'), 'priority' => '0.8'];
        $urls[] = ['loc' => url('epaper'), 'priority' => '0.8'];
        $urls[] = ['loc' => url('contact'), 'priority' => '0.6'];
        $urls[] = ['loc' => url('search'), 'priority' => '0.3'];

        foreach ($articles as $a) {
            $urls[] = [
                'loc' => url('article/'.$a->slug),
                'lastmod' => optional($a->updated_at)->toAtomString(),
                'priority' => '0.7',
            ];
        }

        foreach ($pages as $p) {
            $urls[] = [
                'loc' => url('page/'.$p->slug),
                'lastmod' => optional($p->updated_at)->toAtomString(),
                'priority' => '0.4',
            ];
        }

        $xml = view('public.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
