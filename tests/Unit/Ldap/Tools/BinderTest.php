<?php

namespace Tests\Unit\Ldap\Tools;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Tools\Binder;
use Cruftman\Ldap\Presets\Binding;
use Korowai\Lib\Ldap\Adapter\BindingInterface;


class BinderTest extends TestCase
{
    public function test__bind()
    {
        $arguments = ['foo' => 'FOO'];
        $dn = 'uid=jsmith,ou=people,dc=';
        $pw = 'secret';

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
             ->will($this->returnValue(true));

        $binder = new Binder;

        $this->assertTrue($binder->bind($binding, $ldap, $arguments));
    }
}
