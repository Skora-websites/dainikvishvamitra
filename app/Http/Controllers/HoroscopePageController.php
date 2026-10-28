<?php

namespace App\Http\Controllers;

use App\Models\HoroscopeSign;
use Illuminate\View\View;

class HoroscopePageController extends Controller
{
    public function __invoke(): View
    {
        $signs = HoroscopeSign::orderBy('sort_order')->orderBy('id')->get();

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

        return view('public.horoscope', [
            'nav_active' => 'horoscope',
            'page_title' => 'आज का राशिफल | दैनिक विश्वामित्र',
            'signs' => $signs,
            'zdata' => $zdata,
        ]);
    }
}
