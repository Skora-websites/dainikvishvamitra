<?php

namespace App\Http\View\Composers;

use App\Models\Article;
use App\Models\Bulletin;
use App\Models\Category;
use App\Models\ElectionEvent;
use App\Models\MarketIndex;
use App\Models\Poll;
use App\Models\TickerItem;
use App\Support\Site;
use Illuminate\View\View;

/**
 * Injects data shared by every public page: nav categories, ticker, footer
 * categories, and site settings. Mirrors the legacy header/include dependencies.
 */
class PublicLayoutComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'navCategories' => Category::where('status', 'active')->where('show_in_nav', 1)
                ->orderBy('sort_order')->orderBy('id')->get(),
            'footerCategories' => Category::where('status', 'active')->where('show_in_footer', 1)
                ->orderBy('sort_order')->orderBy('id')->get(),
            'tickerItems' => TickerItem::where('is_active', 1)->where('slot', 'flash')
                ->orderBy('sort_order')->orderBy('id')->get(),
            'ribbonItems' => TickerItem::where('is_active', 1)->where('slot', 'ribbon')
                ->orderBy('sort_order')->orderBy('id')->get(),
            'marketIndices' => MarketIndex::where('is_active', 1)
                ->orderBy('sort_order')->orderBy('id')->get(),
            'siteSettings' => [
                'address' => Site::setting('address'),
                'contact_email' => Site::setting('contact_email'),
                'contact_phone' => Site::setting('contact_phone'),
                'copyright_text' => Site::setting('copyright_text'),
                'edition_line' => Site::setting('edition_line'),
                'reg_no' => Site::setting('reg_no'),
                'facebook' => Site::setting('facebook'),
                'instagram' => Site::setting('instagram'),
                'header_sensex' => Site::setting('header_sensex'),
                'header_nifty' => Site::setting('header_nifty'),
                'weather_text' => Site::setting('weather_text'),
                'seats_nda' => Site::setting('seats_nda', '२९३ सीटें (५४%)'),
                'seats_india' => Site::setting('seats_india', '२३४ सीटें (४३%)'),
                'seats_other' => Site::setting('seats_other', '१६ सीटें (३%)'),
            ],
        ]);
    }

    /**
     * Data needed by partials/sidebar. Depends on the current category when the
     * page provides one (mirrors the legacy `$is_politics` behaviour).
     */
    public static function sidebarData(?int $categoryId = null): array
    {
        // Most-read articles across the site (or within the current category).
        $mostRead = Article::published()
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderByDesc('views')
            ->limit(5)
            ->get([
                'id', 'title', 'slug', 'image', 'image_alt', 'author_name',
                'category_id', 'views', 'time_label', 'published_at',
            ])
            ->map(function ($a) {
                return (object) [
                    'title' => $a->title,
                    'url' => url('article/'.$a->slug),
                    'image' => $a->image,
                    'image_alt' => $a->image_alt,
                    'views' => $a->views,
                    'time_display' => $a->time_display,
                    'cat_name' => optional($a->category)->name_hi,
                ];
            });

        // Active poll for this context: category poll on category pages, else home poll.
        $poll = Poll::where('is_active', 1)
            ->where('context', $categoryId ? 'category' : 'home')
            ->first();
        $pollOptions = $poll ? $poll->options : collect();
        $pollVotes = (int) $pollOptions->sum('votes');

        // Archive blurb: newest article in the category (same shape as most_read).
        $archive = Article::published()
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderByDesc('id')
            ->first(['id', 'title', 'slug', 'image', 'image_alt', 'author_name', 'time_label', 'published_at']);

        if ($archive) {
            $archive = (object) [
                'title' => $archive->title,
                'url' => url('article/'.$archive->slug),
                'image' => $archive->image,
                'image_alt' => $archive->image_alt,
                'views' => $archive->views,
                'time_display' => $archive->time_display,
                'cat_name' => optional($archive->category)->name_hi,
            ];
        }

        // Side bulletins + election calendar.
        $bulletins = Bulletin::where('is_active', 1)
            ->where('placement', 'sidebar')
            ->orderBy('sort_order')->orderBy('id')
            ->limit(4)
            ->get(['id', 'source', 'text', 'time_text']);

        $events = ElectionEvent::where('is_active', 1)
            ->orderBy('sort_order')->orderBy('id')
            ->get(['id', 'state_name', 'date_text']);

        return [
            'most_read' => $mostRead,
            'side_bul' => $bulletins,
            'events' => $events,
            'archive' => $archive,
            'cat_poll' => $poll,
            'cat_poll_opts' => $pollOptions,
            'cat_poll_total' => $pollVotes,
            'cat_poll_voted' => session()->has('poll_voted'),
        ];
    }
}
