<?php

namespace Cruftman\Providers;

use Illuminate\Support\ServiceProvider;

use Cruftman\Console\Commands\ModelMakeCommand;
use Illuminate\Console\Application as Artisan;

class ArtisanServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->extendModelMakeCommand();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Artisan::starting(function ($artisan) {
            $artisan->resolve(ModelMakeCommand::class);
        });
    }

    protected function extendModelMakeCommand()
    {
        $this->app->extend('command.model.make', function ($command, $app) {
            return new ModelMakeCommand($app['files']);
        });
    }
}
