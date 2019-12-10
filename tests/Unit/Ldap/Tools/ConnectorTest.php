<?php

namespace Tests\Unit\Ldap\Tools;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Tools\Connector;
use Cruftman\Ldap\Tools\Binder;
use Cruftman\Ldap\Presets\Connection;
use Cruftman\Ldap\Presets\Session;
use Cruftman\Ldap\Presets\Binding;
use Korowai\Lib\Ldap\Ldap;


class ConnectorTest extends TestCase
{
    public function test__construct__withoutArgs()
    {
        $connector = new Connector;
        $this->assertInstanceOf(Binder::class, $connector->getBinder());
        $this->assertSame([Ldap::class, 'createWithConfig'], $connector->getConstructor());
    }

    public function test__construct__withOneArg()
    {
        $binder = $this->createStub(Binder::class);

        $connector =new Connector($binder);
        $this->assertSame($binder, $connector->getBinder());
        $this->assertSame([Ldap::class, 'createWithConfig'], $connector->getConstructor());

        $connector = new Connector(null);
        $this->assertInstanceOf(Binder::class, $connector->getBinder());
        $this->assertSame([Ldap::class, 'createWithConfig'], $connector->getConstructor());
    }

    public function test__construct__withTwoArgs()
    {
        $binder = $this->createStub(Binder::class);
        $constructor = function () {};

        $connector =new Connector($binder, $constructor);
        $this->assertSame($binder, $connector->getBinder());
        $this->assertSame($constructor, $connector->getConstructor());

        $connector =new Connector($binder, null);
        $this->assertSame($binder, $connector->getBinder());
        $this->assertSame([Ldap::class, 'createWithConfig'], $connector->getConstructor());

        $connector = new Connector(null, null);
        $this->assertInstanceOf(Binder::class, $connector->getBinder());
        $this->assertSame([Ldap::class, 'createWithConfig'], $connector->getConstructor());
    }

    public function test__setBinder()
    {
        $binder = $this->createStub(Binder::class);
        $connector =new Connector;
        $this->assertSame($connector, $connector->setBinder($binder));
        $this->assertSame($binder, $connector->getBinder());
    }

    public function test__setConstructor()
    {
        $constructor = function () {};
        $connector =new Connector;
        $this->assertSame($connector, $connector->setConstructor($constructor));
        $this->assertSame($constructor, $connector->getConstructor());
    }

    public function test__createLdap()
    {
        $arguments = ['foo' => 'FOO'];
        $received = null;

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
                   ->method('config')
                   ->with($arguments)
                   ->will($this->returnCallback(function (array $arguments) {
                       return array_merge($arguments, ['bar' => 'BAR']);
                   }));

        $ldap = $this->createStub(Ldap::class);
        $constructor = function (array $config) use ($ldap, &$received) {
            $received = $config;
            return $ldap;
        };

        $connector = new Connector(null, $constructor);
        $this->assertSame($ldap, $connector->createLdap($connection, $arguments));
        $this->assertSame(['foo' => 'FOO', 'bar' => 'BAR'], $received);
    }

    public function test__createAndBindLdap()
    {
        $arguments = ['foo' => 'FOO'];
        $received = null;

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
                   ->method('config')
                   ->with($arguments)
                   ->will($this->returnCallback(function (array $arguments) {
                       return array_merge($arguments, ['bar' => 'BAR']);
                   }));

        $binding = $this->createStub(Binding::class);


        $ldap = $this->createStub(Ldap::class);
        $constructor = function (array $config) use ($ldap, &$received) {
            $received = $config;
            return $ldap;
        };

        $binder = $this->createStub(Binder::class);
        $binder->expects($this->once())
               ->method('bind')
               ->with($binding, $ldap, $arguments)
               ->will($this->returnValue(true));

        $connector = new Connector($binder, $constructor);
        $this->assertSame($ldap, $connector->createAndBindLdap($connection, $binding, $arguments));
        $this->assertSame(['foo' => 'FOO', 'bar' => 'BAR'], $received);
    }

    public function test__createLdapWithSession()
    {
        $arguments = ['foo' => 'FOO'];
        $received = null;

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
                   ->method('config')
                   ->with($arguments)
                   ->will($this->returnCallback(function (array $arguments) {
                       return array_merge($arguments, ['bar' => 'BAR']);
                   }));

        $binding = $this->createStub(Binding::class);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())
                ->method('connection')
                ->with()
                ->will($this->returnValue($connection));
        $session->expects($this->once())
                ->method('binding')
                ->with()
                ->will($this->returnValue($binding));


        $ldap = $this->createStub(Ldap::class);
        $constructor = function (array $config) use ($ldap, &$received) {
            $received = $config;
            return $ldap;
        };

        $binder = $this->createStub(Binder::class);
        $binder->expects($this->once())
               ->method('bind')
               ->with($binding, $ldap, $arguments)
               ->will($this->returnValue(true));

        $connector = new Connector($binder, $constructor);
        $this->assertSame($ldap, $connector->createLdapWithSession($session, $arguments));
        $this->assertSame(['foo' => 'FOO', 'bar' => 'BAR'], $received);
    }
}
