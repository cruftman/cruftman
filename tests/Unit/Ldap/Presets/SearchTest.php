<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\Search;
use Cruftman\Support\Preset;


class SearchTest extends TestCase
{
    protected function getTestOptions()
    {
        return [
            'base' => '${top},dc=example,dc=org',
            'filter' => 'objectclass=${objectclass}',
            'options' => [ 'scope' => '${scope}' ],
        ];
    }

    public function test__extends__Preset()
    {
        $parents = class_parents(Search::class);
        $this->assertContains(Preset::class, $parents);
    }


    public function test__base()
    {
        $search = new Search($this->getTestOptions());
        $this->assertSame('ou=people,dc=example,dc=org', $search->base(['top' => 'ou=people']));
    }

    public function test__filter()
    {
        $search = new Search($this->getTestOptions());
        $this->assertSame('objectclass=inetOrgPerson', $search->filter(['objectclass' => 'inetOrgPerson']));
    }

    public function test__options()
    {
        $search = new Search($this->getTestOptions());
        $this->assertSame(['scope' => 'one'], $search->options(['scope' => 'one']));
    }
}
