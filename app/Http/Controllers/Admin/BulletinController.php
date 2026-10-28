<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bulletin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BulletinController extends AdminController
{
    public function index()
    {
        return view('admin.bulletins', [
            'home' => Bulletin::where('placement', 'home')->orderBy('sort_order')->orderBy('id')->get(),
            'sidebar' => Bulletin::where('placement', 'sidebar')->orderBy('sort_order')->orderBy('id')->get(),
            'user' => $this->admin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request);
        Bulletin::create([
            'placement' => $data['placement'],
            'source' => $data['source'] ?? null,
            'text' => $data['text'],
            'time_text' => $data['time_text'] ?? null,
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.bulletins')->with('ok', 'बुलेटिन जोड़ा गया।');
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $row = Bulletin::findOrFail((int) $id);
        $data = $this->validate($request);
        $row->fill([
            'placement' => $data['placement'],
            'source' => $data['source'] ?? null,
            'text' => $data['text'],
            'time_text' => $data['time_text'] ?? null,
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ])->save();

        return redirect()->route('admin.bulletins')->with('ok', 'बुलेटिन अपडेट हुआ।');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        Bulletin::findOrFail((int) $id)->delete();

        return redirect()->route('admin.bulletins')->with('ok', 'बुलेटिन हटाया गया।');
    }

    /** @return array<string,mixed> */
    private function validate(Request $request): array
    {
        return $request->validate([
            'placement' => ['required', 'in:home,sidebar'],
            'source' => ['nullable', 'string', 'max:80'],
            'text' => ['required', 'string', 'max:255'],
            'time_text' => ['nullable', 'string', 'max:60'],
        ], [], ['placement' => 'स्थान', 'text' => 'पाठ']);
    }
}
