<?php

namespace App\Http\Controllers;

use App\Models\Epaper;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EpaperController extends Controller
{
    public function index(): View
    {
        $papers = Epaper::where('is_active', 1)
            ->orderByDesc('edition_date')
            ->orderByDesc('id')
            ->get();

        return view('public.epaper', [
            'nav_active' => 'epaper',
            'page_title' => 'ई-पेपर | दैनिक विश्वामित्र',
            'papers' => $papers,
        ]);
    }

    public function show(Request $request, int|string $id): View
    {
        $paper = Epaper::findOrFail((int) $id);

        // Allow previewing inactive editions for authenticated admins
        if (! $paper->is_active && ! $request->session()->has('admin_user')) {
            abort(404);
        }

        $recent = Epaper::where('is_active', 1)
            ->where('id', '!=', $paper->id)
            ->orderByDesc('edition_date')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        return view('public.epaper-reader', [
            'nav_active' => 'epaper',
            'page_title' => ($paper->title ?? 'ई-पेपर').' | दैनिक विश्वामित्र',
            'paper' => $paper,
            'recent' => $recent,
        ]);
    }

    public function __invoke(Request $request): View
    {
        return $this->index();
    }
}
