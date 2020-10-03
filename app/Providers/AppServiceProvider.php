<?php

namespace App\Providers;

use App\Freshbooks;
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
        $this->app->bind(Freshbooks::class, function ($app) {
            return new Freshbooks(config('freshbooks.subdomain'), config('freshbooks.api_token'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
