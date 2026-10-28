<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PollVoteController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        // Laravel's VerifyCsrfToken middleware already gates this POST route
        // (419 on a bad token); no extra check needed here.

        $data = $request->validate([
            'poll_id' => ['required', 'integer', 'exists:polls,id'],
            'option_id' => ['required', 'integer', 'exists:poll_options,id'],
        ], [], ['poll_id' => 'सर्वेक्षण', 'option_id' => 'विकल्प']);

        $poll = Poll::find($data['poll_id']);
        $option = PollOption::where('poll_id', $poll->id)->where('id', $data['option_id'])->first();

        if (! $poll || ! $option) {
            return back()->with('err', 'अमान्य विकल्प।');
        }

        $votedKey = 'voted_'.$poll->id;
        if ($request->cookie($votedKey)) {
            return back()->with('err', 'आप पहले ही मत दे चुके हैं।');
        }

        $option->increment('votes');

        return back()
            ->with('ok', 'आपका मत दर्ज हुआ। धन्यवाद!')
            ->withCookie(cookie($votedKey, '1', 60 * 24));
    }
}
