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

class Application extends \Illuminate\Foundation\Application
{
    /**
     * Create a new Illuminate application instance.
     *
     * @param  string|null  $basePath
     * @param  string|null  $environmentFile
     * @return void
     */
    public function __construct($basePath = null, $environmentFile = null)
    {
        if ($environmentFile !== null) {
            $this->environmentFile = $environmentFile;
        }
        parent::__construct($basePath);
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @param  string  $path
     * @return string
     */
    public function path($path = '')
    {
        $appPath = $this->appPath ?: $this->basePath.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Cruftman';

        return $appPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return 'Cruftman (0.1.0) based on Laravel ' . parent::version();
    }
}

// vim: syntax=php sw=4 ts=4 et:
