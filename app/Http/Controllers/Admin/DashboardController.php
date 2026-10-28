<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Subscriber;

class DashboardController extends AdminController
{
    public function __invoke()
    {
        $stats = [
            'articles' => Article::count(),
            'published' => Article::where('status', 'published')->count(),
            'drafts' => Article::where('status', 'draft')->count(),
            'breaking' => Article::where('is_breaking', true)->count(),
            'categories' => Category::count(),
            'unread' => ContactMessage::where('is_read', false)->count(),
            'messages' => ContactMessage::count(),
            'subs' => Subscriber::where('is_active', true)->count(),
            'views' => (int) Article::sum('views'),
        ];

        $recent = Article::with('category')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $latestMessages = ContactMessage::orderByDesc('id')->limit(5)->get();

        return view('admin.dashboard', [
            'admin_title' => 'डैशबोर्ड · Dashboard',
            'stats' => $stats,
            'recent' => $recent,
            'latestMessages' => $latestMessages,
            'user' => $this->admin(),
        ]);
    }
}
