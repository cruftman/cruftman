<?php
/**
 * @file src/Cruftman/Providers/RoutingServiceProvider.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Providers;

use Illuminate\Support\ServiceProvider;
use Dingo\Api\Routing\ResourceRegistrar as DingoResourceRegistrar;
use Dingo\Api\Routing\Router as DingoRouter;
use Cruftman\Api\Routing\ResourceRegistrar as CruftmanResourceRegistrar;

class RoutingServiceProvider extends ServiceProvider
{
    /**
     * Boot the fractal service for application.
     *
     * @return void
     */
    public function boot()
    {
        $api = $this->app[\Dingo\Api\Routing\Router::class];

        $api->version('v1', [
            'namespace' => 'Cruftman\\Http\\Controllers',
            //'middleware' => 'api.auth'
        ], function ($api) {
            require __DIR__ . '/../../../routes/api.php';
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DingoResourceRegistrar::class, function ($app) {
            return new CruftmanResourceRegistrar($app[DingoRouter::class]);
        });
    }
}

// vim: syntax=php sw=4 ts=4 et:
