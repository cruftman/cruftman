<?php

namespace Cruftman\Providers;

use Illuminate\Support\ServiceProvider;
use CloudCreativity\LaravelJsonApi\LaravelJsonApi;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        LaravelJsonApi::defaultApi('v1');
    }
}
