<?php

namespace App\Providers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
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
       $schedule = $this->app->make(Schedule::class);

       $schedule->call(function(){
        Redis::del('pharmacy:leaderboard');
       })->everyMinute()
       ->name('claer_pharmacy_leaderboard')
       ->onOneServer();
    }
}
