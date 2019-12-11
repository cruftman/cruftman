<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasBinderTool;
use Cruftman\Ldap\Tools\Binder;

class HasBinderToolTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasBinderTool;
        };

        $this->assertInstanceOf(Binder::class, $object->getBinder());

        $stub = $this->createStub(Binder::class);
        $this->assertSame($object, $object->setBinder($stub));

        $this->assertSame($stub, $object->getBinder());

        $this->assertSame($object, $object->setBinder(null));
        $this->assertInstanceOf(Binder::class, $object->getBinder());
    }
}
