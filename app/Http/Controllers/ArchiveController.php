<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    public function __invoke(Request $request): View
    {
        $date = (string) $request->query('date', '');
        $valid = $date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && strtotime($date) !== false;

        $items = null;
        $prev = null;
        $next = null;

        if ($valid) {
            $items = Article::published()
                ->whereDate('published_at', $date)
                ->newest()
                ->paginate(15)
                ->withQueryString();

            $prev = Article::published()->whereDate('published_at', '<', $date)->orderByRaw('published_at DESC')->value('published_at');
            $next = Article::published()->whereDate('published_at', '>', $date)->orderBy('published_at')->value('published_at');
        }

        return view('public.archive', [
            'nav_active' => 'archive',
            'page_title' => 'पुरालेख | दैनिक विश्वामित्र',
            'date' => $date,
            'valid' => $valid,
            'items' => $items,
            'prev' => $prev ? optional($prev)->format('Y-m-d') : null,
            'next' => $next ? optional($next)->format('Y-m-d') : null,
        ]);
    }
}
