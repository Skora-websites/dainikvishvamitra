<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HubController extends Controller
{
    public function breaking(Request $request): View
    {
        return $this->render('breaking', $request);
    }

    public function opinion(Request $request): View
    {
        return $this->render('opinion', $request);
    }

    /**
     * Shared flag-hub pipeline, matching the legacy hub.php contract:
     * the lead is excluded from the paginated list, and the lead only
     * shows on page 1.
     */
    private function render(string $kind, Request $request): View
    {
        $base = Article::published()
            ->where('published_at', '<=', now())
            ->when($kind === 'breaking', fn ($q) => $q->breaking())
            ->when($kind === 'opinion', fn ($q) => $q->opinion());

        $lead = (clone $base)->orderByDesc('published_at')->first();
        $leadId = $lead ? (int) $lead->id : 0;

        $listing = (clone $base)->when($leadId, fn ($q) => $q->where('id', '!=', $leadId));

        $per = 8;
        $total = (clone $listing)->count();
        $pages = max(1, (int) ceil($total / $per));
        $page = max(1, min((int) $request->query('page', 1), $pages));

        $articles = (clone $listing)
            ->orderByDesc('published_at')
            ->offset(($page - 1) * $per)
            ->limit($per)
            ->get();

        $from = $total === 0 ? 0 : ($page - 1) * $per + 1;
        $to = min($total, $page * $per);

        return view('public.'.$kind, [
            'nav_active' => $kind === 'opinion' ? 'editorial-and-opinion' : 'breaking',
            'page_title' => ($kind === 'breaking' ? 'ब्रेकिंग न्यूज़' : 'विचार एवं लेख').' | दैनिक विश्वामित्र',
            'hub_kind' => $kind,
            'lead' => $page === 1 ? $lead : null,
            'articles' => $articles,
            'pagination' => [
                'total' => $total,
                'per' => $per,
                'page' => $page,
                'pages' => $pages,
                'from' => $from,
                'to' => $to,
                'base' => url($kind).'?',
            ],
        ]);
    }
}
