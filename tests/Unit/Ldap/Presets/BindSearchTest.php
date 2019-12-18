<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\BindSearch;
use Cruftman\Ldap\Presets\Search;
use Cruftman\Support\Preset;


class BindSearchTest extends TestCase
{
    public function test__extends__Search()
    {
        $parents = class_parents(BindSearch::class);
        $this->assertContains(Search::class, $parents);
    }

    public function test__base()
    {
        $search = new BindSearch([]);
        $this->assertSame('cn=foo,dc=org', $search->base(['binddn' => 'cn=foo,dc=org']));

        $search = new BindSearch(['base' => '${top},dc=example,dc=org']);
        $this->assertSame('ou=people,dc=example,dc=org', $search->base(['top' => 'ou=people']));
    }

    public function test__filter()
    {
        $search = new BindSearch([]);
        $this->assertSame('objectclass=*', $search->filter([]));

        $search = new BindSearch(['filter' => 'objectclass=${objectclass}']);
        $this->assertSame('objectclass=inetOrgPerson', $search->filter(['objectclass' => 'inetOrgPerson']));
    }

    public function test__options()
    {
        $search = new BindSearch([]);
        $this->assertSame(['scope' => 'base', 'attributes' => ['*']], $search->options([]));

        $search = new BindSearch(['options' => []]);
        $this->assertSame(['scope' => 'base', 'attributes' => ['*']], $search->options([]));

        $search = new BindSearch(['options' => ['scope' => 'base']]);
        $this->assertSame(['scope' => 'base', 'attributes' => ['*']], $search->options([]));

        $search = new BindSearch(['options' => ['attributes' => ['*']]]);
        $this->assertSame(['attributes' => ['*'], 'scope' => 'base'], $search->options([]));

        $search = new BindSearch(['options' => ['scope' => 'sub', 'attributes' => ['xxx']]]);
        $this->assertSame(['scope' => 'sub', 'attributes' => ['xxx']], $search->options([]));
    }

    public function test__defaults()
    {
        $search = new BindSearch([]);
        $this->assertSame('BINDDN', $search->base(['binddn' => 'BINDDN']));
        $this->assertSame('objectclass=*', $search->filter([]));
        $this->assertSame(['scope' => 'base', 'attributes' => ['*']], $search->options([]));
    }
}
