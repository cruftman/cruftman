<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasAuthSchemaPreset;
use Cruftman\Ldap\Preset\AuthSchema as AuthSchemaPreset;

class HasAuthSchemaPresetTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasAuthSchemaPreset;
        };

        $this->assertNull($object->getAuthSchemaPreset());

        $stub = $this->createStub(AuthSchemaPreset::class);
        $this->assertSame($object, $object->setAuthSchemaPreset($stub));

        $this->assertSame($stub, $object->getAuthSchemaPreset());
    }
}
