<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\BindSearch;
use Cruftman\Ldap\Presets\Search;
use Cruftman\Support\Preset;


class BindSearchTest extends TestCase
{
    protected function getTestOptions()
    {
        return [
            'base' => '${top},dc=example,dc=org',
            'filter' => 'objectclass=${objectclass}',
            'options' => [ 'scope' => '${scope}' ],
        ];
    }

    public function test__extends__Search()
    {
        $parents = class_parents(BindSearch::class);
        $this->assertContains(Search::class, $parents);
    }

    public function test__base()
    {
        $search = new BindSearch($this->getTestOptions());
        $this->assertSame('ou=people,dc=example,dc=org', $search->base(['top' => 'ou=people']));
    }

    public function test__filter()
    {
        $search = new BindSearch($this->getTestOptions());
        $this->assertSame('objectclass=inetOrgPerson', $search->filter(['objectclass' => 'inetOrgPerson']));
    }

    public function test__options()
    {
        $search = new BindSearch($this->getTestOptions());
        $this->assertSame(['scope' => 'one'], $search->options(['scope' => 'one']));
    }

    public function test__defaults()
    {
        $search = new BindSearch([]);
        $this->assertSame('BINDDN', $search->base(['binddn' => 'BINDDN']));
        $this->assertSame('objectclass=*', $search->filter([]));
        $this->assertSame(['scope' => 'base', 'attributes' => ['*']], $search->options([]));
    }
}
