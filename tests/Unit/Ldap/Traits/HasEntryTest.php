<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasEntry;
use Korowai\Lib\Ldap\EntryInterface;

class HasEntryTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasEntry;
        };

        $this->assertNull($object->getEntry());

        $stub = $this->createStub(EntryInterface::class);
        $this->assertSame($object, $object->setEntry($stub));

        $this->assertSame($stub, $object->getEntry());
    }
}
