<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Page;
use App\Support\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    public function show()
    {
        $page = Page::where('slug', 'contact')->where('status', 'published')->first();

        return view('public.contact', [
            'nav_active' => 'contact',
            'page_title' => 'संपर्क | दैनिक विश्वामित्र',
            'page_desc' => Site::excerpt($page->body ?? '', 160),
            'page' => $page,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
        ], [], [
            'name' => 'नाम', 'email' => 'ईमेल', 'phone' => 'फ़ोन',
            'subject' => 'विषय', 'message' => 'संदेश',
        ]);

        ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'is_read' => false,
        ]);

        return back()->with('ok', 'आपका संदेश प्राप्त हुआ। धन्यवाद!');
    }
}
