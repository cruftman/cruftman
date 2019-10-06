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
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use Dingo\Api\Transformer\Adapter\Fractal;
use Dingo\Api\Transformer\Factory;


class FractalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Manager::class, function ($app) {
            $fractal = new Manager;
            $fractal->setSerializer(new JsonApiSerializer);
            return $fractal;
        });

        $this->app->bind(Fractal::class, function ($app) {
            $fractal = $app->make(Manager::class);
            return new Fractal($fractal);
        });
    }
}

// vim: syntax=php sw=4 ts=4 et:
