<?php

namespace App\Http\Controllers\Admin;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PollController extends AdminController
{
    public function index()
    {
        return view('admin.polls', [
            'rows' => Poll::with('options')->orderByDesc('id')->get(),
            'user' => $this->admin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'context' => ['required', 'in:home,category'],
            'options' => ['nullable', 'string', 'max:2000'],
        ], [], ['question' => 'प्रश्न', 'context' => 'संदर्भ']);

        $poll = Poll::create([
            'question' => $data['question'],
            'context' => $data['context'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->createOptions($poll, (string) $data['options']);

        return redirect()->route('admin.polls')->with('ok', 'जनमत सर्वेक्षण जोड़ा गया।');
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $poll = Poll::findOrFail((int) $id);
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'context' => ['required', 'in:home,category'],
        ], [], ['question' => 'प्रश्न']);

        $poll->fill([
            'question' => $data['question'],
            'context' => $data['context'],
            'is_active' => $request->boolean('is_active'),
        ])->save();

        return redirect()->route('admin.polls')->with('ok', 'सर्वेक्षण अपडेट हुआ।');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        Poll::findOrFail((int) $id)->delete(); // options cascade

        return redirect()->route('admin.polls')->with('ok', 'सर्वेक्षण हटाया गया।');
    }

    public function resetVotes(int|string $id): RedirectResponse
    {
        PollOption::where('poll_id', $id)->update(['votes' => 0]);

        return redirect()->route('admin.polls')->with('ok', 'मत गिनती रीसेट हुई।');
    }

    public function addOption(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'poll_id' => ['required', 'integer', 'exists:polls,id'],
            'option_text' => ['required', 'string', 'max:160'],
        ], [], ['option_text' => 'विकल्प']);

        $order = (int) PollOption::where('poll_id', $data['poll_id'])->max('sort_order') + 1;

        PollOption::create([
            'poll_id' => (int) $data['poll_id'],
            'option_text' => $data['option_text'],
            'sort_order' => $order,
            'color_class' => $request->input('color_class') ?: 'bg-primary',
        ]);

        return redirect()->route('admin.polls')->with('ok', 'विकल्प जोड़ा गया।');
    }

    public function deleteOption(int|string $id): RedirectResponse
    {
        PollOption::where('id', $id)->delete();

        return redirect()->route('admin.polls')->with('ok', 'विकल्प हटाया गया।');
    }

    /** Newline-separated option text on creation. */
    private function createOptions(Poll $poll, string $raw): void
    {
        $order = 0;
        foreach (preg_split('/\r\n|\r|\n/', $raw) ?: [] as $line) {
            $text = trim($line);
            if ($text === '') {
                continue;
            }
            PollOption::create([
                'poll_id' => $poll->id,
                'option_text' => mb_substr($text, 0, 160),
                'sort_order' => $order++,
                'color_class' => 'bg-primary',
            ]);
        }
    }
}
