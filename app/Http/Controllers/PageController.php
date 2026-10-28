<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\Site;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __invoke(string $slug): View
    {
        $page = Page::where('slug', $slug)->where('status', 'published')->firstOrFail();

        return view('public.page', [
            'nav_active' => 'page-'.$slug,
            'page_title' => $page->title.' | दैनिक विश्वामित्र',
            'page_desc' => Site::excerpt($page->body, 160),
            'page' => $page,
        ]);
    }
}
