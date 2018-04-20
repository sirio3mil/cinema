<?php

namespace App\Providers;

use App\Auth\Passwords\CinemaPasswordBrokerManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('auth.password', function ($app) { return new CinemaPasswordBrokerManager($app); });

        $this->app->bind('auth.password.broker', function ($app) { return $app->make('auth.password')->broker(); });
    }
}
