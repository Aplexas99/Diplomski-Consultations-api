<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    Carbon::setLocale('hr');
        //check that app is local
if ($this->app->isLocal()) {
    //if local register your services you require for development
    } else {
    //else register your services you require for production
        $this->app['request']->server->set('HTTPS', true);
    }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
