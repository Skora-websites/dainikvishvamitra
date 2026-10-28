<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    public function show()
    {
        return view('public.subscribe', [
            'nav_active' => 'subscribe',
            'page_title' => 'सदस्यता लें | दैनिक विश्वामित्र',
            'msg' => null,
            'ok' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:160'],
        ], [], ['email' => 'ईमेल']);

        $existing = Subscriber::where('email', strtolower(trim($data['email'])))->first();
        if ($existing) {
            return back()->with('err', 'यह ईमेल पहले से सदस्यता में है।');
        }

        Subscriber::create(['email' => strtolower(trim($data['email'])), 'is_active' => true]);

        return back()->with('ok', 'न्यूज़लेटर सदस्यता सफल। धन्यवाद!');
    }
}
