<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\Connection;
use Cruftman\Support\Preset;


class ConnectionTest extends TestCase
{
    public function test__extends__Preset()
    {
        $parents = class_parents(Connection::class);
        $this->assertContains(Preset::class, $parents);
    }

    public function test__ldapConfig()
    {
        $options = ['uri' => 'ldap://${server}'];
        $connection = new Connection($options);
        $config = ['uri' => 'ldap://ldap1.example.org'];
        $this->assertSame($config, $connection->ldapConfig(['server' => 'ldap1.example.org']));
    }
}
