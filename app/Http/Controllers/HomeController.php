<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\HoroscopeSign;
use App\Models\MarketIndex;
use App\Models\Poll;
use App\Models\TickerItem;
use App\Support\SlotFiller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $slots = new SlotFiller;

        $heroList = $slots->take('home_slot', 'hero', 1);
        $hero = $heroList->first();

        $spots = $slots->take('home_slot', 'spot', 2);
        $market = $slots->take('home_slot', 'market', 4, 'business-and-markets');

        $polLeadList = $slots->take('home_slot', 'pol_lead', 1, 'politics', true);
        $pol_lead = $polLeadList->first();
        $pol_list = $slots->take('home_slot', 'pol_list', 3, 'politics');

        $sportLeadList = $slots->take('home_slot', 'sport_lead', 1, 'sports', true);
        $sport_lead = $sportLeadList->first();
        $sport_list = $slots->take('home_slot', 'sport_list', 3, 'sports');

        $ent = $slots->take('home_slot', 'ent', 2, 'entertainment');
        $real = $slots->take('home_slot', 'real', 2, 'real-estate');

        $signs = HoroscopeSign::orderBy('sort_order')->orderBy('id')->limit(12)->get();

        $poll = Poll::where('context', 'home')->where('is_active', 1)
            ->with('options')
            ->orderByDesc('id')
            ->first();
        $poll_opts = $poll ? $poll->options : collect();
        $poll_total = (int) $poll_opts->sum('votes');

        $zkeys = ['mesh', 'vrish', 'mithun', 'kark', 'singh', 'kanya', 'tula', 'vrishchik', 'dhanu', 'makar', 'kumbh', 'meen'];
        $zdata = [];
        foreach ($signs as $i => $s) {
            $zdata[$zkeys[$i] ?? ('s'.$i)] = [
                'sign' => $s->symbol,
                'title' => $s->name_hi.' राशि ('.$s->name_en.')',
                'desc' => $s->prediction,
                'color' => $s->lucky_color,
                'num' => $s->lucky_number,
                'deity' => $s->deity,
            ];
        }

        return view('public.index', [
            'nav_active' => 'home',
            'page_title' => 'दैनिक विश्वामित्र | ताज़ा खबर, राजनीति, व्यापार, खेल',
            // Legacy variable names kept verbatim (index.blade.php contract).
            'flash' => TickerItem::where('is_active', 1)->where('slot', 'flash')->orderBy('sort_order')->orderBy('id')->get(),
            'bul_home' => Bulletin::where('placement', 'home')->where('is_active', 1)->orderBy('sort_order')->orderBy('id')->limit(5)->get(),
            'indices' => MarketIndex::where('is_active', 1)->orderBy('sort_order')->orderBy('id')->limit(4)->get(),
            'hero' => $hero,
            'spots' => $spots,
            'market' => $market,
            'pol_lead' => $pol_lead,
            'pol_list' => $pol_list,
            'sport_lead' => $sport_lead,
            'sport_list' => $sport_list,
            'ent' => $ent,
            'real' => $real,
            'signs' => $signs,
            'poll' => $poll,
            'poll_opts' => $poll_opts,
            'poll_total' => $poll_total,
            'voted' => $poll && request()->cookie('voted_'.$poll->id),
            'zdata' => $zdata,
            'zkeys' => $zkeys,
        ]);
    }

    /**
     * Stat badge markup, ported 1:1 from legacy stat_badge_html().
     */
    public static function statBadgeHtml(?string $stat): string
    {
        $stat = trim((string) $stat);
        if ($stat === '') {
            return '';
        }
        if (str_contains($stat, '+') && ! str_contains($stat, '₹')) {
            return '<span class="text-market-up flex items-center text-xs font-bold"><span class="material-symbols-outlined text-[14px]">trending_up</span> '.e($stat).'</span>';
        }
        if ($stat === 'समझौता') {
            return '<span class="text-market-up text-xs font-bold flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">check_circle</span> '.e($stat).'</span>';
        }
        if (str_contains($stat, '₹')) {
            return '<span class="text-primary font-bold text-xs">'.e($stat).'</span>';
        }

        return '<span class="text-ink-muted text-xs font-semibold">'.e($stat).'</span>';
    }
}
