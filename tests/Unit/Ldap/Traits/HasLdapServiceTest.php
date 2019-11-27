<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasLdapService;
use Cruftman\Ldap\Service;

class HasLdapServiceTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasLdapService;
        };

        $this->assertNull($object->getLdapService());

        $stub = $this->createStub(Service::class);
        $this->assertSame($object, $object->setLdapService($stub));

        $this->assertSame($stub, $object->getLdapService());
    }
}
