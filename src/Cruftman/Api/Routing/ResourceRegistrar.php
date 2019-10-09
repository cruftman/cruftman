<?php
/**
 * @file src/Cruftman/Api/Routing/ResourceRegistrar.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Api\Routing;

use Dingo\Api\Routing\ResourceRegistrar as DingoResourceRegistrar;

class ResourceRegistrar extends DingoResourceRegistrar
{
    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     *
     * @return void
     */
    public function register($name, $controller, array $options = [])
    {
        if (($transformers = $options['transform'] ?? null) !== null) {
            $this->registerTransformers($name, $transformers);
        }
        return parent::register($name, $controller, $options);
    }

    protected function registerTransformers($name, $transformers)
    {
        foreach ($transformers as $class => $binding) {
            $this->registerTransformer($name, $class, $binding);
        }
    }

    protected function registerTransformer($name, $class, $binding)
    {
        if (is_string($binding) || is_callable($binding) || is_object($binding)) {
            $binding = [ $binding ];
        } elseif (! is_array($binding)) {
            throw new \InvalidArgumentException("\$binding must be a string, callable, object or an array");
        }

        $resolver = $binding['resolver'] ?? $binding[0];
        $defaults = ['key' => ($binding['key'] ?? $name)];
        $parameters = $binding['parameters'] ?? [];
        $parameters = array_merge($defaults, $parameters);

        $args = [$class, $resolver, $parameters];

        if (($after = $binding['after'] ?? null) !== null) {
            $args[] = $after;
        }

        app('api.transformer')->register(...$args);
    }
}

// vim: syntax=php sw=4 ts=4 et:
