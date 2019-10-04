<?php
/**
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\cruftman
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends Testing\TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/web/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }
}

// vim: syntax=php sw=4 ts=4 et:
