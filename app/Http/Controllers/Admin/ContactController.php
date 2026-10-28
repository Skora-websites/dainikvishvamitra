<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends AdminController
{
    public function index(Request $request)
    {
        $rows = ContactMessage::orderByDesc('id')->paginate(20);
        $viewId = (int) $request->query('view', 0);
        $view = $viewId > 0 ? ContactMessage::find($viewId) : null;

        return view('admin.messages', [
            'rows' => $rows,
            'view' => $view,
            'unread' => ContactMessage::where('is_read', false)->count(),
            'user' => $this->admin(),
        ]);
    }

    public function markRead(int|string $id): RedirectResponse
    {
        ContactMessage::where('id', $id)->update(['is_read' => true]);

        return back()->with('ok', 'संदेश पढ़ा हुआ चिह्नित।');
    }

    public function markAllRead(): RedirectResponse
    {
        ContactMessage::where('is_read', false)->update(['is_read' => true]);

        return back()->with('ok', 'सभी संदेश पढ़े हुए चिह्नित।');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        ContactMessage::where('id', $id)->delete();

        return back()->with('ok', 'संदेश हटा दिया गया।');
    }
}
