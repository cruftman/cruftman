<?php
/**
 * @file src/Cruftman/Api/Routing/Router.php
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
        return parent::register($name, $controller, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
