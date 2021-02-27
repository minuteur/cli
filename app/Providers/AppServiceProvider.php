<?php

namespace App\Providers;

use App\Clockify;
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

        $this->app->bind(Clockify::class, function ($app) {
            return new Clockify(config('clockify.api_key'), config('clockify.workspace_id'));
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
