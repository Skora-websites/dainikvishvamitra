<?php

namespace App\Http\Controllers\Admin;

use App\Models\HoroscopeSign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HoroscopeController extends AdminController
{
    public function index()
    {
        return view('admin.horoscope', [
            'rows' => HoroscopeSign::orderBy('sort_order')->orderBy('id')->get()->keyBy('id'),
            'user' => $this->admin(),
        ]);
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $sign = HoroscopeSign::findOrFail((int) $id);

        $data = $request->validate([
            'name_hi' => ['required', 'string', 'max:40'],
            'name_en' => ['required', 'string', 'max:40'],
            'symbol' => ['required', 'string', 'max:8'],
            'prediction' => ['required', 'string', 'max:5000'],
            'lucky_color' => ['nullable', 'string', 'max:40'],
            'lucky_number' => ['nullable', 'string', 'max:20'],
            'deity' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer'],
        ], [], [
            'name_hi' => 'नाम (हिंदी)',
            'name_en' => 'नाम (अंग्रेज़ी)',
            'symbol' => 'चिह्न',
            'prediction' => 'भविष्यवाणी',
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $sign->fill($data)->save();

        return redirect()->route('admin.horoscope')->with('ok', $sign->name_hi.' अपडेट हुआ।');
    }
}
