<?php

declare(strict_types=1);

namespace Cruftman\Tests;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../../../bootstrap/app.php';
    }
}

// vim: syntax=php sw=4 ts=4 et:
