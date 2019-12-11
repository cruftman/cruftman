<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasAuthStatus;
use Cruftman\Ldap\Auth\Status;

class HasAuthStatusTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasAuthStatus;
        };

        $this->assertInstanceOf(Status::class, $object->getAuthStatus());

        $stub = $this->createStub(Status::class);
        $this->assertSame($object, $object->setAuthStatus($stub));

        $this->assertSame($stub, $object->getAuthStatus());

        $this->assertSame($object, $object->setAuthStatus(null));
        $this->assertInstanceOf(Status::class, $object->getAuthStatus());
    }
}
