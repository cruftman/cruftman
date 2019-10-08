<?php
/**
 * @file src/Cruftman/Providers/LumenServiceProvider.php
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

class LumenServiceProvider extends ServiceProvider
{
    /**
     * Boot the fractal service for application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->configure('api');
        $this->app->configure('swagger-lume');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Order matters: (1) first their providers...
        $this->app->register(\Dingo\Api\Provider\LumenServiceProvider::class);
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class,);
        $this->app->register(\SwaggerLume\ServiceProvider::class);

        // ... and (2) then our own to overwrite some of their bindings.
        $this->app->register(FractalServiceProvider::class);
        $this->app->register(RoutingServiceProvider::class);
    }
}

// vim: syntax=php sw=4 ts=4 et:
