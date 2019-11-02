<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasLdapInterface;
use Cruftman\Ldap\Traits\ProvidesLdapInterface;
use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\EntryInterface;
use Korowai\Lib\Ldap\Adapter\AdapterInterface;
use Korowai\Lib\Ldap\Adapter\BindingInterface;
use Korowai\Lib\Ldap\Adapter\EntryManagerInterface;
use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\CompareQueryInterface;

class ProvidesLdapInterfaceTest extends TestCase
{
    public function getTestObject(?LdapInterface $ldap)
    {
        $object = new class implements LdapInterface {
            use ProvidesLdapInterface;
        };
        if ($ldap !== null) {
            $object->setLdapInterface($ldap);
        }
        return $object;
    }

    public function test__HasLdapInterface()
    {
        $traits = class_uses(ProvidesLdapInterface::class);
        $this->assertContains(HasLdapInterface::class, $traits);
    }

    public function test__definesLdapInterfaceMethods()
    {
        // The ProvidesLdapInterface trait must implement all methods of the
        // LdapInterface. Otherwise this code will throw an exception.
        $object = new class implements LdapInterface {
            use ProvidesLdapInterface;
        };

        $this->assertInstanceOf(LdapInterface::class, $object);
    }

    public function test__getAdapter()
    {
        $adapter = $this->createStub(AdapterInterface::class);
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('getAdapter')
             ->with()
             ->willReturn($adapter);

        $object = $this->getTestObject($ldap);
        $this->assertSame($adapter, $object->getAdapter());
    }

    public function test__getBinding()
    {
        $adapter = $this->createStub(BindingInterface::class);
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('getBinding')
             ->with()
             ->willReturn($adapter);

        $object = $this->getTestObject($ldap);
        $this->assertSame($adapter, $object->getBinding());
    }

    public function test__getEntryManager()
    {
        $adapter = $this->createStub(EntryManagerInterface::class);
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('getEntryManager')
             ->with()
             ->willReturn($adapter);

        $object = $this->getTestObject($ldap);
        $this->assertSame($adapter, $object->getEntryManager());
    }

    public function test__isBound()
    {
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('isBound')
             ->with()
             ->willReturn(true);

        $object = $this->getTestObject($ldap);
        $this->assertTrue($object->isBound());
    }

    public function test__bind()
    {
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->exactly(3))
             ->method('bind')
             ->withConsecutive(['cn=admin,dc=example,dc=org', 'admin'],
                               ['cn=admin,dc=example,dc=org'],
                               [])
             ->willReturn(true);

        $object = $this->getTestObject($ldap);
        $this->assertTrue($object->bind('cn=admin,dc=example,dc=org', 'admin'));
        $this->assertTrue($object->bind('cn=admin,dc=example,dc=org'));
        $this->assertTrue($object->bind());
    }

    public function test__unbind()
    {
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('unbind')
             ->with()
             ->willReturn('ok');

        $object = $this->getTestObject($ldap);
        $this->assertSame('ok', $object->unbind());
    }

    public function test__add()
    {
        $entry = $this->createStub(EntryInterface::class);
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('add')
             ->with($entry)
             ->willReturn('ok');

        $object = $this->getTestObject($ldap);
        $this->assertSame('ok', $object->add($entry));
    }

    public function test__update()
    {
        $entry = $this->createStub(EntryInterface::class);
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('update')
             ->with($entry)
             ->willReturn('ok');

        $object = $this->getTestObject($ldap);
        $this->assertSame('ok', $object->update($entry));
    }

    public function test__rename()
    {
        $entry = $this->createStub(EntryInterface::class);
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('rename')
             ->with($entry, 'foo', true)
             ->willReturn('ok');

        $object = $this->getTestObject($ldap);
        $this->assertSame('ok', $object->rename($entry, 'foo', true));
    }

    public function test__delete()
    {
        $entry = $this->createStub(EntryInterface::class);
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('delete')
             ->with($entry)
             ->willReturn('ok');

        $object = $this->getTestObject($ldap);
        $this->assertSame('ok', $object->delete($entry));
    }

    public function test__createSearchQuery()
    {
        $query = $this->createStub(SearchQueryInterface::class);
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('createSearchQuery')
             ->with('dc=example,dc=org', 'objectclass=*', ['scope' => 'one'])
             ->willReturn($query);

        $object = $this->getTestObject($ldap);
        $this->assertSame($query, $object->createSearchQuery('dc=example,dc=org', 'objectclass=*', ['scope' => 'one']));
    }

    public function test__createCompareQuery()
    {
        $query = $this->createStub(CompareQueryInterface::class);
        $ldap = $this->createMock(LdapInterface::class);
        $ldap->expects($this->once())
             ->method('createCompareQuery')
             ->with('cn=admin,dc=example,dc=org', 'userpassword', 'admin')
             ->willReturn($query);

        $object = $this->getTestObject($ldap);
        $this->assertSame($query, $object->createCompareQuery('cn=admin,dc=example,dc=org', 'userpassword', 'admin'));
    }
}
