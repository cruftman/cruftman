<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\AuthAttempt;
use Cruftman\Ldap\Presets\Connection;
use Cruftman\Ldap\Presets\Binding;
use Cruftman\Ldap\Presets\Aggregate;
use Cruftman\Ldap\Presets\BindSearch;
use Cruftman\Support\Preset;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;


class AuthAttemptTest extends TestCase
{
    public function test__extends__Preset()
    {
        $parents = class_parents(AuthAttempt::class);
        $this->assertContains(Preset::class, $parents);
    }

    public function test__binding__missing()
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('"binding"');
        new AuthAttempt([]);
    }

    public function test__binding()
    {
        $options = ['binding' => ['cn=admin,dc=example,dc=org', 'secret']];
        $aat = new AuthAttempt($options, new Aggregate([]));

        $binding = $aat->binding();
        $this->assertInstanceOf(Binding::class, $binding);
        $this->assertSame(['cn=admin,dc=example,dc=org', 'secret'], $binding->getOptions()->getArrayCopy());
    }

    public function test__binding__ref()
    {
        $options = ['binding' => 'admin'];
        $aat = new AuthAttempt($options, new Aggregate([
            'bindings' => ['admin' => ['cn=admin,dc=example,dc=org', 'secret']],
        ]));

        $binding = $aat->binding();
        $this->assertInstanceOf(Binding::class, $binding);
        $this->assertSame(['cn=admin,dc=example,dc=org', 'secret'], $binding->getOptions()->getArrayCopy());
    }

    public function test__connections()
    {
        $options = ['binding' => [], 'connections' => [['uri' => 'ldap://ldap1.example.org'], 'ldap2']];
        $aat = new AuthAttempt($options, new Aggregate([
            'connections' => ['ldap2' => ['uri' => 'ldap://ldap2.example.org']],
        ]));
        $connections = $aat->connections();
        $this->assertIsArray($connections);
        $this->assertCount(2, $connections);
        $this->assertInstanceOf(Connection::class, $connections[0]);
        $this->assertSame(['uri' => 'ldap://ldap1.example.org'], $connections[0]->getOptions()->getArrayCopy());
        $this->assertInstanceOf(Connection::class, $connections[1]);
        $this->assertSame(['uri' => 'ldap://ldap2.example.org'], $connections[1]->getOptions()->getArrayCopy());
    }

    public function test__connections__null()
    {
        $options = ['binding' => []];
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertNull($aat->connections([]));
    }

    public function test__search()
    {
        $options = ['binding' => [], 'search' => ['base' => 'FOO', 'filter' => 'BAR', 'options' => ['GEEZ']]];
        $aat = new AuthAttempt($options, new Aggregate([]));

        $search = $aat->search();
        $this->assertInstanceOf(BindSearch::class, $search);
        $this->assertSame('FOO', $search->base([]));
        $this->assertSame('BAR', $search->filter([]));
        $this->assertSame(['GEEZ'], $search->options([]));
    }

    public function test__search__default()
    {
        $options = ['binding' => [], 'search' => []];
        $aat = new AuthAttempt($options, new Aggregate([]));

        $search = $aat->search();
        $this->assertInstanceOf(BindSearch::class, $search);
        $this->assertSame('cn=foo,dc=bar', $search->base(['binddn' => 'cn=foo,dc=bar']));
        $this->assertSame('objectclass=*', $search->filter([]));
        $this->assertSame(['scope' => 'base', 'attributes' => ['*']], $search->options([]));
    }

    public function test__search__null()
    {
        $options = ['binding' => []];
        $aat = new AuthAttempt($options, new Aggregate([]));

        $this->assertNull($aat->search());
    }

//    public function test__filter()
//    {
//        $options = ['binding' => [], 'filter' => 'uid=${username}'];
//        $aat = new AuthAttempt($options, new Aggregate([]));
//
//        $this->assertSame('uid=jsmith', $aat->filter(['username' => 'jsmith']));
//    }
//
//    public function test__filter__null()
//    {
//        $options = ['binding' => []];
//        $aat = new AuthAttempt($options, new Aggregate([]));
//
//        $this->assertNull($aat->filter([]));
//    }
//
//    public function test__attributes()
//    {
//        $options = ['binding' => [], 'attributes' => ['${username}', 'cn']];
//        $aat = new AuthAttempt($options, new Aggregate([]));
//
//        $this->assertSame(['uid', 'cn'], $aat->attributes(['username' => 'uid']));
//    }
//
//    public function test__attributes__null()
//    {
//        $options = ['binding' => []];
//        $aat = new AuthAttempt($options, new Aggregate([]));
//
//        $this->assertNull($aat->attributes([]));
//    }
}
