<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasConnectionPreset;
use Cruftman\Ldap\Preset\Connection as ConnectionPreset;

class HasConnectionPresetTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasConnectionPreset;
        };

        $this->assertNull($object->getConnectionPreset());

        $stub = $this->createStub(ConnectionPreset::class);
        $this->assertSame($object, $object->setConnectionPreset($stub));

        $this->assertSame($stub, $object->getConnectionPreset());
    }
}
