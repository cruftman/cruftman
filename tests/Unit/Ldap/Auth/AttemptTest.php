<?php

namespace Tests\Unit\Ldap\Auth;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Auth\Attempt;
use Cruftman\Ldap\Preset\AuthAttempt as Preset;

//use Cruftman\Ldap\Auth\Source;
//use Korowai\Lib\Ldap\LdapInterface;
//use Cruftman\Ldap\Preset\Connection;

class AttemptTest extends TestCase
{
    public function test__construct()
    {
        $preset = $this->createStub(Preset::class);
        $attempt = new Attempt($preset);
        $this->assertSame($preset, $attempt->getAuthAttemptPreset());
    }

}
