<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasAuthAttemptPreset;
use Cruftman\Ldap\Preset\AuthAttempt as AuthAttemptPreset;

class HasAuthAttemptPresetTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasAuthAttemptPreset;
        };

        $this->assertNull($object->getAuthAttemptPreset());

        $stub = $this->createStub(AuthAttemptPreset::class);
        $this->assertSame($object, $object->setAuthAttemptPreset($stub));

        $this->assertSame($stub, $object->getAuthAttemptPreset());
    }
}
