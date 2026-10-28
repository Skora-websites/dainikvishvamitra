<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\View\View;

class VideoPageController extends Controller
{
    public function __invoke(): View
    {
        $videos = Video::where('is_active', 1)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('public.video', [
            'nav_active' => 'video',
            'page_title' => 'वीडियो | दैनिक विश्वामित्र',
            'videos' => $videos,
            'featured' => $videos->first(),
        ]);
    }
}
