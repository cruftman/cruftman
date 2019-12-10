<?php

namespace Tests\Unit\Ldap\Tools;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Tools\Failover;
use Korowai\Lib\Ldap\Exception\LdapException;


class FailoverTest extends TestCase
{
    public function test__construct__withOneArg()
    {
        $callback = function () {};
        $failover = new Failover($callback);
        $this->assertSame($callback, $failover->getCallback());
        $this->assertNull($failover->getFallback());
    }

    public function test__construct__withTwoArgs()
    {
        $callback = function () {};
        $fallback = function () {};
        $failover = new Failover($callback, $fallback);
        $this->assertSame($callback, $failover->getCallback());
        $this->assertSame($fallback, $failover->getFallback());
    }

    public function test__setCallback()
    {
        $callback1 = function () {};
        $callback2 = function () {};

        $failover = new Failover($callback1);
        $this->assertSame($failover, $failover->setCallback($callback2));
        $this->assertSame($callback2, $failover->getCallback());
    }

    public function test__setFallback()
    {
        $callback = function () {};
        $fallback = function () {};

        $failover = new Failover($callback);
        $this->assertSame($failover, $failover->setFallback($fallback));
        $this->assertSame($fallback, $failover->getFallback());
    }

    public function test__tryWith__firstSucceds()
    {
        $callCount = 0;
        $callProvider = null;
        $providers = ['P1', 'P2'];

        $callback = function (string $provider) use (&$callCount, &$callProvider) {
            $callCount += 1;
            $callProvider = $provider;
            return 'OK '.$provider;
        };

        $fallback = function (array $providers) {
            throw \RuntimeException('This function should never be called');
        };

        $failover = new Failover($callback, $fallback);
        $this->assertSame('OK P1', $failover->tryWith($providers));
        $this->assertSame(1, $callCount);
        $this->assertSame($providers[0], $callProvider);
    }

    public function test__tryWith__firstFailsRecoverably()
    {
        $callCount = 0;
        $callProvider = null;
        $providers = ['P1', 'P2'];

        $callback = function (string $provider) use (&$callCount, &$callProvider) {
            $callCount += 1;
            $callProvider = $provider;
            if ($provider == 'P1') {
                throw new LdapException("recoverable error", -1);
            } else {
                return 'OK '.$provider;
            }
        };

        $fallback = function (array $providers) {
            throw \RuntimeException('This function should never be called');
        };

        $failover = new Failover($callback, $fallback);
        $this->assertSame('OK P2', $failover->tryWith($providers));
        $this->assertSame(2, $callCount);
        $this->assertSame($providers[1], $callProvider);
    }

    public function test__tryWith__allFailRecoverably()
    {
        $callCount = 0;
        $callProvider = null;
        $providers = ['P1', 'P2'];

        $callback = function (string $provider) use (&$callCount, &$callProvider) {
            $callCount += 1;
            $callProvider = $provider;
            throw new LdapException("recoverable error", -1);
        };

        $fallback = function (array $providers) {
            return 'NOK '.implode(' ', $providers);
        };

        $failover = new Failover($callback, $fallback);
        $this->assertSame('NOK P1 P2', $failover->tryWith($providers));
        $this->assertSame(2, $callCount);
        $this->assertSame($providers[1], $callProvider);
    }

    public function test__tryWith__allFailRecoverablyWithoutFallback()
    {
        $callCount = 0;
        $callProvider = null;
        $providers = ['P1', 'P2'];

        $callback = function (string $provider) use (&$callCount, &$callProvider) {
            $callCount += 1;
            $callProvider = $provider;
            throw new LdapException("recoverable error", -1);
        };

        $failover = new Failover($callback);
        $this->assertNull($failover->tryWith($providers));
        $this->assertSame(2, $callCount);
        $this->assertSame($providers[1], $callProvider);
    }

    public function test__tryWith__firstFailsUnrecoverably()
    {
        $providers = ['P1', 'P2'];

        $callback = function (string $provider) {
            if ($provider == 'P1') {
                throw new LdapException("unrecoverable error (protocol error)", 0x02);
            } else {
                return 'OK '.$provider;
            }
        };

        $fallback = function (array $providers) {
            throw \RuntimeException('This function should never be called');
        };

        $failover = new Failover($callback, $fallback);

        $this->expectException(LdapException::class);
        $this->expectExceptionMessage("unrecoverable error (protocol error)");
        $this->expectExceptionCode(0x02);

        $failover->tryWith($providers);
    }
}
