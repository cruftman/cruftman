<?php
/**
 * @file src/Cruftman/Ldap/AuthServiceProvider.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/ldap.php', 'ldap');
        $this->app->singleton(LdapServiceInterface::class, function ($app) {
            return new AuthService($app['config']->get('ldap'));
        });
        $this->app->alias(LdapServiceInterface::class, 'cruftman.ldap');
    }
}

// vim: syntax=php sw=4 ts=4 et:
