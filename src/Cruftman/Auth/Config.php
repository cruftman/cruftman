<?php
/**
 * @file src/Cruftman/Auth/Config.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Auth;

class Config
{
    /**
     * @var array
     */
    protected $connections;

    /**
     * @var array
     */
    protected $bindings;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var array
     */
    protected $searches;

    /**
     * @var array
     */
    protected $users;

    public function __construct(array $config)
    {
        $this->connections = $config['connections'];
        $this->bindings = $config['bindngs'];
        $this->searches = $config['searches'];
        $this->filters = $config['filters'];
    }
}

// vim: syntax=php sw=4 ts=4 et:
