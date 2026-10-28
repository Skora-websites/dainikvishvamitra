<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Support\SlotFiller;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __invoke(string $slug): View
    {
        $cat = Category::where('slug', $slug)->where('status', 'active')->first();

        if (! $cat) {
            abort(404, 'श्रेणी नहीं मिली।');
        }

        $slots = new SlotFiller;

        // Lead article: prefixed by design so the grid starts fresh.
        $lead = Article::published()
            ->where('cat_slot', 'lead')
            ->whereHas('category', fn ($q) => $q->where('slug', $slug))
            ->orderBy('cat_order')->orderByDesc('published_at')
            ->first();

        if ($lead) {
            $slots->reserve($lead->id);
        } else {
            $lead = Article::published()
                ->whereHas('category', fn ($q) => $q->where('slug', $slug))
                ->newest()
                ->first();
            if ($lead) {
                $slots->reserve($lead->id);
            }
        }

        $grid = $slots->take('cat_slot', 'grid', 4, $slug);
        $dossier = $slots->take('cat_slot', 'dossier', 4, $slug);

        $feed = Article::published()
            ->whereHas('category', fn ($q) => $q->where('slug', $slug))
            ->newest()
            ->paginate(8)
            ->withQueryString();

        $pills = array_values(array_filter(array_map('trim', explode(',', (string) $cat->pills))));

        return view('public.category', [
            'nav_active' => $slug,
            'page_title' => $cat->name_hi.' समाचार | दैनिक विश्वामित्र',
            'cat' => $cat,
            'lead' => $lead,
            'grid' => $grid,
            'dossier' => $dossier,
            'feed' => $feed,
            'pills' => $pills,
        ]);
    }
}
