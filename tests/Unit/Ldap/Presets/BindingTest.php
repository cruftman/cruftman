<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\Binding;
use Cruftman\Support\Preset;


class BindingTest extends TestCase
{
    public function test__extends__Preset()
    {
        $parents = class_parents(Binding::class);
        $this->assertContains(Preset::class, $parents);
    }

    public function test__dn()
    {
        $options = ['uid=${username},dc=example,dc=org', '${password}'];
        $binding = new Binding($options);
        $this->assertSame('uid=jsmith,dc=example,dc=org', $binding->dn(['username' => 'jsmith']));
    }

    public function test__password()
    {
        $options = ['uid=${username},dc=example,dc=org', '${password}'];
        $binding = new Binding($options);
        $this->assertSame('secret', $binding->password(['password' => 'secret']));
    }
}
