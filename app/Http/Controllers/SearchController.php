<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $results = Article::published()
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('title_en', 'like', "%{$q}%")
                        ->orWhere('excerpt', 'like', "%{$q}%")
                        ->orWhere('excerpt_en', 'like', "%{$q}%")
                        ->orWhere('author_name', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->newest()
            ->paginate(12)
            ->withQueryString();

        return view('public.search', [
            'nav_active' => 'search',
            'page_title' => $q !== '' ? 'खोज: '.$q.' | दैनिक विश्वामित्र' : 'खोज | दैनिक विश्वामित्र',
            'q' => $q,
            'results' => $results,
        ]);
    }
}
