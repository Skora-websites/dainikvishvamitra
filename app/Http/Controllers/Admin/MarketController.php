<?php

namespace App\Http\Controllers\Admin;

use App\Models\MarketIndex;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MarketController extends AdminController
{
    public function index()
    {
        return view('admin.market', [
            'rows' => MarketIndex::orderBy('sort_order')->orderBy('id')->get(),
            'user' => $this->admin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request);
        MarketIndex::create([
            'label' => $data['label'],
            'value_text' => $data['value_text'],
            'change_text' => $data['change_text'],
            'direction' => $data['direction'],
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.market')->with('ok', 'सूचकांक जोड़ा गया।');
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $row = MarketIndex::findOrFail((int) $id);
        $data = $this->validate($request);
        $row->fill([
            'label' => $data['label'],
            'value_text' => $data['value_text'],
            'change_text' => $data['change_text'],
            'direction' => $data['direction'],
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ])->save();

        return redirect()->route('admin.market')->with('ok', 'सूचकांक अपडेट हुआ।');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        MarketIndex::findOrFail((int) $id)->delete();

        return redirect()->route('admin.market')->with('ok', 'सूचकांक हटाया गया।');
    }

    /** @return array<string,mixed> */
    private function validate(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'value_text' => ['required', 'string', 'max:60'],
            'change_text' => ['required', 'string', 'max:60'],
            'direction' => ['required', 'in:up,down'],
        ], [], ['label' => 'नाम', 'value_text' => 'मान', 'change_text' => 'परिवर्तन', 'direction' => 'दिशा']);
    }
}
