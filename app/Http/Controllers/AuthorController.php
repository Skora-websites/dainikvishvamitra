<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function __invoke(string $name): View
    {
        $name = rawurldecode($name);

        $articles = Article::published()
            ->where('author_name', $name)
            ->newest()
            ->paginate(10)
            ->withQueryString();

        return view('public.author', [
            'nav_active' => 'author',
            'page_title' => $name.' | दैनिक विश्वामित्र',
            'name' => $name,
            'articles' => $articles,
        ]);
    }
}
