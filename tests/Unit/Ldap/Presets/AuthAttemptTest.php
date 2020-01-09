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
    /**
     * Provides 'filtering' and 'fetching' options that shall make
     * the isSearchRequested() to return true.
     */
    public function ffEnabling()
    {
        return [
            [[]],
            [['filtering' => true]],
            [['filtering' => false]],
            [['fetching' => true]],
            [['fetching' => false]],
            [['filtering' => true, 'fetching' => false]],
            [['filtering' => false, 'fetching' => true]],
        ];
    }

    /**
     * Provides 'filtering' and 'fetching' options that shall make
     * the isSearchRequested() to return false.
     */
    public function ffDisabling()
    {
        return [
            [['filtering' => false, 'fetching' => false]],
        ];
    }

    /**
     * Provides 'filtering' and 'fetching' options -- all possible combinations.
     */
    public function ffAll()
    {
        return array_merge(
            $this->ffEnabling(),
            $this->ffDisabling()
        );
    }

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
        $options = ['binding' => [], 'search' => ['base' => 'FOO', 'filter' => 'BAR', 'options' => []]];
        $aat = new AuthAttempt($options, new Aggregate([]));

        $search = $aat->search();
        $this->assertInstanceOf(BindSearch::class, $search);
        $this->assertSame('FOO', $search->base([]));
        $this->assertSame('BAR', $search->filter([]));
        $this->assertIsArray($search->options([]));
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

    public function test__fetching__null()
    {
        $options = ['binding' => []];
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertNull($aat->fetching([]));
    }

    public function test__fetching__false()
    {
        $options = ['binding' => [], 'fetching' => false];
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertFalse($aat->fetching([]));
    }

    public function test__fetching__true()
    {
        $options = ['binding' => [], 'fetching' => true];
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertTrue($aat->fetching([]));
    }

    public function test__filtering__null()
    {
        $options = ['binding' => []];
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertNull($aat->filtering([]));
    }

    public function test__filtering__false()
    {
        $options = ['binding' => [], 'filtering' => false];
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertFalse($aat->filtering([]));
    }

    public function test__filtering__true()
    {
        $options = ['binding' => [], 'filtering' => true];
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertTrue($aat->filtering([]));
    }

    /**
     * @dataProvider ffEnabling
     */
    public function test__isSearchRequested__true(array $ff)
    {
        $options = array_merge(['binding' => []], $ff);
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertTrue($aat->isSearchRequested([]));
    }

    /**
     * @dataProvider ffDisabling
     */
    public function test__isSearchRequested__false(array $ff)
    {
        $options = array_merge(['binding' => []], $ff);
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertFalse($aat->isSearchRequested([]));
    }

    /**
     * @dataProvider ffAll
     */
    public function test__getSearchIfRequested__missing(array $ff)
    {
        $options = array_merge(['binding' => []], $ff);
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertNull($aat->getSearchIfRequested([]));
    }

    /**
     * @dataProvider ffDisabling
     */
    public function test__getSearchIfRequested__null(array $ff)
    {
        $options = array_merge(['binding' => [], 'search' => []], $ff);
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertNull($aat->getSearchIfRequested([]));
    }

    /**
     * @dataProvider ffEnabling
     */
    public function test__getSearchIfRequested__nonNull(array $ff)
    {
        $options = array_merge(['binding' => [], 'search' => []], $ff);
        $aat = new AuthAttempt($options, new Aggregate([]));
        $this->assertInstanceOf(BindSearch::class, $aat->getSearchIfRequested([]));
    }
}
