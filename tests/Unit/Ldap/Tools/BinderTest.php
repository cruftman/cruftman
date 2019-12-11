<?php

namespace Tests\Unit\Ldap\Tools;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Tools\Binder;
use Cruftman\Ldap\Presets\Binding;
use Korowai\Lib\Ldap\Adapter\BindingInterface;
use Korowai\Lib\Ldap\Exception\LdapException;


class BinderTest extends TestCase
{
    protected function getMockedArgs(string $dn, string $pw, $bindWill)
    {
        $arguments = ['foo' => 'FOO'];

        $binding = $this->createMock(Binding::class);
        $binding->expects($this->once())
                ->method('dn')
                ->with($arguments)
                ->will($this->returnValue($dn));
        $binding->expects($this->once())
                ->method('password')
                ->with($arguments)
                ->will($this->returnValue($pw));

        $ldap = $this->getMockBuilder(BindingInterface::class)->getMock();
        $ldap->expects($this->once())
             ->method('bind')
             ->with($dn, $pw)
             ->will($bindWill);

        return [$binding, $ldap, $arguments];
    }

    public function test__bind()
    {
        $dn = 'uid=jsmith,ou=people,dc=example,dc=org';
        $pw = 'secret';
        [$binding, $ldap, $arguments] = $this->getMockedArgs($dn, $pw, $this->returnValue(true));
        $this->assertTrue((new Binder)->bind($binding, $ldap, $arguments));
    }

    public function test__bindDn__trueResult()
    {
        $dn = 'uid=jsmith,dc=foo';
        $pw = 'secret';
        [$binding, $ldap, $arguments] = $this->getMockedArgs($dn, $pw, $this->returnValue(true));

        $this->assertTrue((new Binder)->bindDn($binding, $ldap, $arguments, $dnRet));
        $this->assertSame($dn, $dnRet);
    }

    public function test__bindDn__falseResult()
    {
        $dn = 'uid=jsmith,dc=foo';
        $pw = 'secret';
        [$binding, $ldap, $arguments] = $this->getMockedArgs($dn, $pw, $this->returnValue(false));
        $this->assertFalse((new Binder)->bindDn($binding, $ldap, $arguments, $dnRet));
        $this->assertSame($dn, $dnRet);
    }

    public function test__bindDn__LdapException()
    {
        $dn = 'uid=jsmith,dc=foo';
        $pw = 'secret';
        $exception = new LdapException("test message", 0x123);
        [$binding, $ldap, $arguments] = $this->getMockedArgs($dn, $pw, $this->throwException($exception));

        $this->expectException(LdapException::class);
        $this->expectExceptionMessage("test message");
        $this->expectExceptionCode(0x123);

        $result = null;
        try {
            $result = (new Binder)->bindDn($binding, $ldap, $arguments, $dnRet);
        } catch (LdapException $caught) {
            $this->assertSame($exception, $caught);
            $this->assertNull($result);
            $this->assertSame($dn, $dnRet);
            throw $caught;
        }
    }
}
