<?php

namespace App\Http\Controllers\Admin;

use App\Models\ElectionEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ElectionController extends AdminController
{
    public function index()
    {
        return view('admin.elections', [
            'rows' => ElectionEvent::orderBy('sort_order')->orderBy('id')->get(),
            'user' => $this->admin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request);
        ElectionEvent::create([
            'state_name' => $data['state_name'],
            'date_text' => $data['date_text'],
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.elections')->with('ok', 'चुनाव कार्यक्रम जोड़ा गया।');
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $row = ElectionEvent::findOrFail((int) $id);
        $data = $this->validate($request);
        $row->fill([
            'state_name' => $data['state_name'],
            'date_text' => $data['date_text'],
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ])->save();

        return redirect()->route('admin.elections')->with('ok', 'कार्यक्रम अपडेट हुआ।');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        ElectionEvent::findOrFail((int) $id)->delete();

        return redirect()->route('admin.elections')->with('ok', 'कार्यक्रम हटाया गया।');
    }

    /** @return array<string,mixed> */
    private function validate(Request $request): array
    {
        return $request->validate([
            'state_name' => ['required', 'string', 'max:80'],
            'date_text' => ['required', 'string', 'max:80'],
        ], [], ['state_name' => 'राज्य', 'date_text' => 'दिनांक']);
    }
}
