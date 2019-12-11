<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasFinderTool;
use Cruftman\Ldap\Tools\Finder;

class HasFinderToolTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasFinderTool;
        };

        $this->assertInstanceOf(Finder::class, $object->getFinder());

        $stub = $this->createStub(Finder::class);
        $this->assertSame($object, $object->setFinder($stub));

        $this->assertSame($stub, $object->getFinder());

        $this->assertSame($object, $object->setFinder(null));
        $this->assertInstanceOf(Finder::class, $object->getFinder());
    }
}
