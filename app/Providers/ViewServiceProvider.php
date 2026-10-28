<?php

namespace App\Providers;

use App\Http\View\Composers\PublicLayoutComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('layouts.public', PublicLayoutComposer::class);
        View::composer('partials.*', function ($view) {
            // Partials included directly from a page inherit its variables; only the
            // standalone partials (sidebar) need the shared data.
            if ($view->name() === 'partials.sidebar') {
                (new PublicLayoutComposer)->compose($view);
            }
        });
    }
}
