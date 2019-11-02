<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasLdapInterface;
use Korowai\Lib\Ldap\LdapInterface;

class HasLdapInterfaceTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasLdapInterface;
        };

        $this->assertNull($object->getLdapInterface());

        $ldap = $this->createStub(LdapInterface::class);

        $this->assertSame($object, $object->setLdapInterface($ldap));
        $this->assertSame($ldap, $object->getLdapInterface());
        $this->assertSame($object, $object->setLdapInterface(null));
        $this->assertNull($object->getLdapInterface());
    }

    public function test__createsLdapInterface()
    {
        $ldap = $this->createStub(LdapInterface::class);
        $object = new class ($ldap) {
            use HasLdapInterface;
            public function __construct($ldap) {
                $this->storedLdap = $ldap;
            }
            protected function createLdapInterface() : LdapInterface {
                return $this->storedLdap;
            }
        };

        $this->assertSame($ldap, $object->getLdapInterface());
        $this->assertSame($object, $object->setLdapInterface(null));
        $this->assertSame($ldap, $object->getLdapInterface());
    }
}
