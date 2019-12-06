<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\Session;
use Cruftman\Ldap\Presets\Connection;
use Cruftman\Ldap\Presets\Binding;
use Cruftman\Ldap\Presets\Aggregate;
use Cruftman\Support\Preset;


class SessionTest extends TestCase
{
    public function test__extends__Preset()
    {
        $parents = class_parents(Session::class);
        $this->assertContains(Preset::class, $parents);
    }

    public function test__connection()
    {
        $options = ['connection' => ['uri' => 'ldap://ldap1.example.org']];
        $session = new Session($options, new Aggregate([]));
        $connection = $session->connection();
        $this->assertInstanceOf(Connection::class, $connection);
        $this->assertSame(['uri' => 'ldap://ldap1.example.org'], $connection->substOptions());
    }

    public function test__connection__ref()
    {
        $options = ['connection' => 'ldap1' ];
        $session = new Session($options, new Aggregate([
            'connections' => [ 'ldap1' => ['uri' => 'ldap://ldap1.example.org'] ]
        ]));
        $connection = $session->connection();
        $this->assertInstanceOf(Connection::class, $connection);
        $this->assertSame(['uri' => 'ldap://ldap1.example.org'], $connection->substOptions());
    }

    public function test__binding()
    {
        $options = ['connection' => [], 'binding' => ['cn=admin,dc=example,dc=org', 'secret']];
        $session = new Session($options, new Aggregate([]));
        $binding = $session->binding();
        $this->assertInstanceOf(Binding::class, $binding);
        $this->assertSame(['cn=admin,dc=example,dc=org', 'secret'], $binding->substOptions());
    }

    public function test__binding__ref()
    {
        $options = ['connection' => [], 'binding' => 'admin'];
        $session = new Session($options, new Aggregate([
            'bindings' => ['admin' => ['cn=admin,dc=example,dc=org', 'secret']]
        ]));
        $binding = $session->binding();
        $this->assertInstanceOf(Binding::class, $binding);
        $this->assertSame(['cn=admin,dc=example,dc=org', 'secret'], $binding->substOptions());
    }

    public function test__binding__null()
    {
        $options = ['connection' => []];
        $session = new Session(['connection' => []]);
        $this->assertNull($session->binding());
    }
}
