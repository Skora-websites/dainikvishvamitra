<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends AdminController
{
    /**
     * Settings are free-form key/value pairs. The allowlist prevents arbitrary
     * keys (and therefore injection into unrelated features) from being written.
     */
    private const ALLOWED = [
        'address', 'contact_email', 'contact_phone', 'copyright_text', 'edition_date',
        'site_name', 'tagline',
        'edition_line', 'epaper_url', 'facebook', 'instagram', 'reg_no',
        'header_sensex', 'header_nifty', 'weather_text',
        'seats_nda', 'seats_india', 'seats_other',
        'seats_nda_pct', 'seats_india_pct', 'seats_other_pct',
        'site_title', 'site_desc', 'youtube_channel', 'youtube', 'twitter',
    ];

    public function index()
    {
        return view('admin.settings', [
            'user' => $this->admin(),
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        // Only persist keys the form actually submitted. `only()` returns null
        // for absent fields, which would otherwise create empty-string rows
        // (leaking keys like 'twitter' that were never seeded).
        $incoming = [];

        foreach ($request->all() as $key => $value) {
            if (! in_array($key, self::ALLOWED, true) || $value === null || ! is_scalar($value)) {
                continue;
            }
            $incoming[$key] = (string) $value;
        }

        foreach ($incoming as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings')->with('ok', 'सेटिंग्स सहेजी गईं।');
    }
}
