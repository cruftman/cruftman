<?php

namespace Cruftman\Providers;

use Illuminate\Support\ServiceProvider;
use Cruftman\Ldap\Service as LdapService;

class LdapServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LdapService::class, function ($app) {
            return new LdapService($app->config->get('ldap'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
