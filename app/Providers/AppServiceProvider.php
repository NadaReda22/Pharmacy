<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;


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
        RateLimiter::for('register' ,function($request)
        {
            return Limit::perMinute(1)->by($request->user()->id?: $request->ip());
        });

              RateLimiter::for('login' ,function($request)
        {
            return Limit::perMinute(3)->by(optional($request->user())->id?: $request->ip());
        });

        RateLimiter::for('live-search', function(Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('notify', fn(Request $r) => Limit::perMinute(5)->by($r->user()?->id ?? $r->ip()));


    }
}
