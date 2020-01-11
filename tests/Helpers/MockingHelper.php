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
    abstract function never();
    abstract function once();
    abstract function exactly(int $num);
    abstract function returnCallback($callback);

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
            } elseif (($willReturn = $config['willReturn'] ?? null) !== null) {
                $em->willReturn($willReturn);
            } elseif (($callback = $config['callbak'] ?? null) !== null) {
                $em->will($this->returnCallback($callback));
            }
        } elseif (is_int($config)) {
            $mock->expects($this->exactly($config))->method($method);
        } elseif ($config === 'once') {
            $mock->expects($this->once())->method($method);
        } elseif ($config === 'never') {
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
            if (array_key_exists($method, $config)) {
                $this->configureMockMethod($mock, $method, $config[$method]);
            }
        }
    }

    /**
     * Given a mock or stub configure its *$method* such that it invokes *$callback*.
     *
     * The *$callback* may either be a callable or a value. If it's a callable, then
     * the *$method* will call that *$callback*. Otherwise, *$method* will be
     * generated to return *$callback* value.
     *
     * Example::
     *
     *      // $mock->next(x) will return x+1
     *      $this->configureMockCallbackMethod($mock, 'next', function (int x) { return x + 1; });
     *      // $mock->two() will always return 2
     *      $this->configureMockCallbackMethod($mock, 'two', 2);
     *
     * @param  object $mock
     * @param  string $methods
     * @param  mixed $callback
     */
    protected function configureMockCallbackMethod($mock, string $method, $callback)
    {
        if (!is_callable($callback)) {
            $callback = function (...$args) use ($callback) {
                return $callback;
            };
        }
        $mock->expects($this->any())
             ->method($method)
             ->will($this->returnCallback($callback));
    }

    /**
     * Given a mock or stub configure its *$methods* such that they invoke *$callbacks*.
     *
     * Example::
     *
     *      // $mock->next(x) will return x+1
     *      // $mock->two() will always return 2
     *      // $mock->other() will be left unconfigured
     *      $this->configureMockCallbackMethods(
     *          $mock,
     *          ['next', 'two', 'other'],
     *          [
     *              'next' => function (int x) { return x + 1; },
     *              'two' => 2
     *          ]
     *      );
     *
     * @param  object $mock
     * @param  string $methods
     * @param  mixed $callback
     */
    protected function configureMockCallbackMethods($mock, array $methods, array $callbacks)
    {
        foreach ($methods as $method) {
            if (array_key_exists($method, $callbacks)) {
                $this->configureMockCallbackMethod($mock, $method, $callbacks[$method]);
            }
        }
    }

    /**
     * @todo Write documentation
     */
    protected function configureMock($mock, array $methods, array $config)
    {
        $this->configureMockMethods($mock, $methods, $config['methods'] ?? []);
        $this->configureMockCallbackMethods($mock, $methods, $config['callbacks'] ?? []);
    }
}

// vim: syntax=php sw=4 ts=4 et:
