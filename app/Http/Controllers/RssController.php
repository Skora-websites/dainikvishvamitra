<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RssController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $feedUrl = url('rss');
        $siteUrl = url('/');

        $items = Article::published()->newest()->limit(20)->get(['id', 'title', 'slug', 'excerpt', 'author_name', 'image', 'published_at']);

        $xml = view('public.rss', [
            'siteUrl' => $siteUrl,
            'feedUrl' => $feedUrl,
            'items' => $items,
        ])->render();

        return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']);
    }
}
