<?php
/**
 * @file src/Cruftman/Application/Application.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Application;

class Application extends \Laravel\Lumen\Application
{
    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return 'Cruftman (0.1.0) based on: ' . parent::version();
    }
}

// vim: syntax=php sw=4 ts=4 et:
