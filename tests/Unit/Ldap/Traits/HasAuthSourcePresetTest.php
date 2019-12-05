<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasAuthSourcePreset;
use Cruftman\Ldap\Presets\AuthSource as AuthSourcePreset;

class HasAuthSourcePresetTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasAuthSourcePreset;
        };

        $this->assertNull($object->getAuthSourcePreset());

        $stub = $this->createStub(AuthSourcePreset::class);
        $this->assertSame($object, $object->setAuthSourcePreset($stub));

        $this->assertSame($stub, $object->getAuthSourcePreset());
    }
}
