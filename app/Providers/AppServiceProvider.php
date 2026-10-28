<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Brute-force protection on admin login (per username+IP).
        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(8)->by(
                strtolower((string) $request->input('username')).'|'.$request->ip()
            );
        });

        // Contact / subscribe / poll submissions (per IP).
        RateLimiter::for('public-forms', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
