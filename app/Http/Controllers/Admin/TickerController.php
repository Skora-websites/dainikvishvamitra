<?php

namespace App\Http\Controllers\Admin;

use App\Models\TickerItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TickerController extends AdminController
{
    public function index()
    {
        return view('admin.ticker', [
            'ribbon' => TickerItem::where('slot', 'ribbon')->orderBy('sort_order')->orderBy('id')->get(),
            'flash' => TickerItem::where('slot', 'flash')->orderBy('sort_order')->orderBy('id')->get(),
            'user' => $this->admin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request);
        TickerItem::create([
            'slot' => $data['slot'],
            'text' => $data['text'],
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.ticker')->with('ok', 'टिकर आइटम जोड़ा गया।');
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $row = TickerItem::findOrFail((int) $id);
        $data = $this->validate($request);
        $row->fill([
            'slot' => $data['slot'],
            'text' => $data['text'],
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ])->save();

        return redirect()->route('admin.ticker')->with('ok', 'टिकर आइटम अपडेट हुआ।');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        TickerItem::findOrFail((int) $id)->delete();

        return redirect()->route('admin.ticker')->with('ok', 'टिकर आइटम हटाया गया।');
    }

    /** @return array<string,mixed> */
    private function validate(Request $request): array
    {
        return $request->validate([
            'slot' => ['required', 'in:ribbon,flash'],
            'text' => ['required', 'string', 'max:255'],
        ], [], ['slot' => 'स्लॉट', 'text' => 'पाठ']);
    }
}
