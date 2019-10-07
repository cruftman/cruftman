<?php
/**
 * @file src/Cruftman/Providers/FractalServiceProvider.php
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
use League\Fractal\Manager as FractalManager;
use League\Fractal\Serializer\JsonApiSerializer;
use Dingo\Api\Transformer\Adapter\Fractal as FractalAdapter;

class FractalServiceProvider extends ServiceProvider
{
    /**
     * Boot the fractal service for application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->configure('api');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(JsonApiSerializer::class, function ($app) {
            $domain = $app['config']->get('api.domain');
            $prefix = $app['config']->get('api.prefix');
            $url = rtrim(implode('/', [$domain, $prefix]), '/');
            return new JsonApiSerializer($url);
        });

        $this->app->bind(FractalManager::class, function ($app) {
            $fractal = new FractalManager;
            $serializer = $app->make(JsonApiSerializer::class);
            $fractal->setSerializer($serializer);
            return $fractal;
        });

        $this->app->bind(FractalAdapter::class, function ($app) {
            $fractal = $app->make(FractalManager::class);
            return new FractalAdapter($fractal);
        });
    }
}

// vim: syntax=php sw=4 ts=4 et:
