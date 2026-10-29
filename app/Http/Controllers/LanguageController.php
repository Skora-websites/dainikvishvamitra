<?php

namespace App\Http\Controllers;

use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Store the visitor's display language in the session and return to the
     * page they were on (falls back home for direct visits).
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, [Locale::HI, Locale::EN], true), 404);

        $request->session()->put('locale', $locale);

        return back();
    }
}
