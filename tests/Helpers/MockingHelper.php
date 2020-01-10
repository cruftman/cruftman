<?php
/**
 * @file tests/Helpers/MockingHelper.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Tests\Helpers;

/**
 * Methods that facilitate mocking.
 */
trait MockingHelper
{
    /**
     * Given a mock or stub configure its *$method* according to *$config*
     *
     * @param  object $mock
     * @param  string $method
     * @param  mixed $config
     */
    protected function configureMockMethod($mock, string $method, $config)
    {
        if (is_array($config)) {
            $times = $config['times'] ?? $this->any();
            if (is_int($times)) {
                $times = $this->exactly($times);
            }
            $em = $mock->expects($times)->method($method);
            if (($with = $config['with'] ?? null) !== null) {
                $em->with(...$with);
            }
            if (($withConsecutive = $config['withConsecutive'] ?? null) !== null) {
                $em->withConsecutive(...$withConsecutive);
            }
            if (($will = $config['will'] ?? null) !== null) {
                $em->will($will);
            }
            if (($willReturn = $config['willReturn'] ?? null) !== null) {
                $em->willReturn($willReturn);
            }
        } else if (is_int($config)) {
            $mock->expects($this->exactly($config))->method($method);
        } else if ($config === 'once') {
            $mock->expects($this->once())->method($method);
        } else if ($config === 'never') {
            $mock->expects($this->never())->method($method);
        }
    }

    /**
     * Given a mock or stub configure its *$methods* according to *$config*
     *
     * @param  object $mock
     * @param  array $methods
     * @param  array $config
     */
    protected function configureMockMethods($mock, array $methods, array $config)
    {
        foreach ($methods as $method) {
            $this->configureMockMethod($mock, $method, $config[$method] ?? null);
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
