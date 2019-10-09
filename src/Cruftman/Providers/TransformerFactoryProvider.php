<?php
/**
 * @file src/Cruftman/Providers/TransformerFactoryProvider.php
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
use Dingo\Api\Transformer\Factory as DingoTransformerFactory;
use Cruftman\Api\Transformer\Factory as CruftmanTransformerFactory;

class TransformerFactoryProvider extends ServiceProvider
{
    /**
     * Boot the fractal service for application.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('api.transformer', CruftmanTransformerFactory::class);
        $this->app->singleton('api.transformer', function ($app) {
            $transformer = $app['config']->get('api.transformer');
            if (is_string($transformer)) {
                $transformer = $app->make($transformer);
            }
            return new CruftmanTransformerFactory($app, $transformer);
        });
    }
}

// vim: syntax=php sw=4 ts=4 et:
