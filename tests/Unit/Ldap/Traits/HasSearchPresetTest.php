<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasSearchPreset;
use Cruftman\Ldap\Presets\Search as SearchPreset;

class HasSearchPresetTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasSearchPreset;
        };

        $this->assertNull($object->getSearchPreset());

        $stub = $this->createStub(SearchPreset::class);
        $this->assertSame($object, $object->setSearchPreset($stub));

        $this->assertSame($stub, $object->getSearchPreset());
    }
}
