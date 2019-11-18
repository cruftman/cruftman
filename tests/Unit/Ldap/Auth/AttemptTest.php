<?php

namespace Tests\Unit\Ldap\Auth;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Auth\Attempt;
use Cruftman\Ldap\Auth\Status;
use Cruftman\Ldap\Service;
use Cruftman\Ldap\Preset\AuthAttempt as AuthAttemptPreset;
use Cruftman\Ldap\Preset\Binding as BindingPreset;
use Cruftman\Ldap\Traits\HasAuthAttemptPreset;

//use Cruftman\Ldap\Auth\Source;
use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\Exception\LdapException;
use Cruftman\Ldap\Preset\Connection as ConnectionPreset;

class AttemptTest extends TestCase
{
    protected function createService(array $options = null)
    {
        if ($options === null) {
            $options = ['auth_schema' => []];
        }
        return new Service($options);
    }

    protected function createAuthAttemptPreset(array $options, ?Service $service = null)
    {
        if ($service === null) {
            $service = $this->createService();
        }
        return $service->getAuthAttempt($options);
    }

    protected function createAttemptWithOptions(array $options, ?Service $service = null)
    {
        $preset = $this->createAuthAttemptPreset($options, $service);
        return $this->createAttemptWithPreset($preset);
    }

    protected function createAttemptWithPreset(AuthAttemptPreset $preset)
    {
        return new Attempt($preset);
    }

    protected function connectionPresetMock($arguments, $createLdapWill)
    {
        $connection = $this->createMock(ConnectionPreset::class);
        $connection->expects($this->once())
                   ->method('createLdap')
                   ->with($arguments)
                   ->will($createLdapWill);
        return $connection;
    }

    protected function ldapInterfaceMock($dn, $password, $bindWill)
    {
        $ldap = $this->getMockBuilder(LdapInterface::class)->getMock();
        $ldap->expects($this->once())
             ->method('bind')
             ->with($dn, $password)
             ->will($bindWill);
        return $ldap;
    }

    protected function bindingPresetMock($ldap, $arguments, $bindLdapInterfaceWill, $getBindDnWill = null)
    {
        $binding = $this->createMock(BindingPreset::class);
        $binding->expects($this->once())
                ->method('bindLdapInterface')
                ->with($this->identicalTo($ldap), $arguments)
                ->will($bindLdapInterfaceWill);
        if ($getBindDnWill === null) {
            $binding->expects($this->never())
                    ->method('getBindDn');
        } else {
            $binding->expects($this->once())
                    ->method('getBindDn')
                    ->with($arguments)
                    ->will($getBindDnWill);
        }
        return $binding;
    }

    protected function authAttemptPresetMock($getConnectionsWill, $getBindingWill, $iterations = 1)
    {
        $preset = $this->createMock(AuthAttemptPreset::class);
        $preset->expects($this->once())
               ->method('getConnections')
               ->with()
               ->will($getConnectionsWill);
        $preset->expects($this->exactly($iterations))
               ->method('getBinding')
               ->with()
               ->will($getBindingWill);
        return $preset;
    }

    public function test__uses__hasAttemptPreset()
    {
        $uses = class_uses(Attempt::class);
        $this->assertContains(HasAuthAttemptPreset::class, $uses);
    }

    public function test__construct()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);
        $attempt = new Attempt($preset);
        $this->assertSame($preset, $attempt->getAuthAttemptPreset());
    }

    public function test__bind__withConnectionArg__trueResult()
    {
        $options = ['bind' => ['uid=${username},dc=foo', '${password}']];
        $attempt = $this->createAttemptWithOptions($options);

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->ldapInterfaceMock('uid=jsmith,dc=foo', 'secret', $this->returnValue(true));
        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));

        $status = new Status();

        $this->assertTrue($attempt->bind($status, $arguments, $connection));
        $this->assertTrue($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withConnectionArg__falseResult()
    {
        $options = ['bind' => ['uid=${username},dc=foo', '${password}']];
        $attempt = $this->createAttemptWithOptions($options);

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->ldapInterfaceMock(
            'uid=jsmith,dc=foo', 'secret',
            $this->returnValue(false)
        );

        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));

        $status = new Status();

        $this->assertFalse($attempt->bind($status, $arguments, $connection));
        $this->assertFalse($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withConnectionArg__invalidCredentialsException()
    {
        $options = ['bind' => ['uid=${username},dc=foo', '${password}']];
        $attempt = $this->createAttemptWithOptions($options);

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->ldapInterfaceMock(
            'uid=jsmith,dc=foo', 'secret',
            $this->throwException(new LdapException('Invalid Credentials', 0x31))
        );

        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));

        $status = new Status();

        $this->assertFalse($attempt->bind($status, $arguments, $connection));
        $this->assertFalse($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withConnectionArg__recoverableException()
    {
        $options = ['bind' => ['uid=${username},dc=foo', '${password}']];
        $attempt = $this->createAttemptWithOptions($options);

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->ldapInterfaceMock(
            'uid=jsmith,dc=foo', 'secret',
            $this->throwException(new LdapException("can't connect to LDAP server", -1))
        );

        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));

        $status = new Status();

        $this->assertFalse($attempt->bind($status, $arguments, $connection));
        $this->assertNull($status->getBindResult());
        $this->assertNull($status->getBindDn());
        $this->assertNull($status->getBindLdap());
        $this->assertNull($status->getBindConnection());
    }

    public function test__bind__withConnectionArg__unrecoverableException()
    {
        $options = ['bind' => ['uid=${username},dc=foo', '${password}' ]];
        $attempt = $this->createAttemptWithOptions($options);

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->ldapInterfaceMock(
            'uid=jsmith,dc=foo', 'secret',
            $this->throwException(new LdapException('Invalid syntax', 0x15))
        );

        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));

        $status = new Status();

        $this->expectException(LdapException::class);
        $this->expectExceptionMessage('Invalid syntax');
        $this->expectExceptionCode(0x15);

        $attempt->bind($status, $arguments, $connection);
    }

    public function test__bind__withSingleConnectionOption__trueResult()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->createStub(LdapInterface::class);

        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));
        $binding = $this->bindingPresetMock($ldap, $arguments, $this->returnValue(true), $this->returnValue('uid=jsmith,dc=foo'));
        $preset = $this->authAttemptPresetMock($this->returnValue([$connection]), $this->returnValue($binding));
        $attempt = $this->createAttemptWithPreset($preset);

        $status = new Status();

        $this->assertTrue($attempt->bind($status, $arguments));
        $this->assertTrue($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withSingleConnectionOption__falseResult()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->createStub(LdapInterface::class);

        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));
        $binding = $this->bindingPresetMock($ldap, $arguments, $this->returnValue(false), $this->returnValue('uid=jsmith,dc=foo'));
        $preset = $this->authAttemptPresetMock($this->returnValue([$connection]), $this->returnValue($binding));
//        $preset = $this->createMock(AuthAttemptPreset::class);
//        $preset->expects($this->once())
//               ->method('getConnections')
//               ->with()
//               ->willReturn([$connection]);
//        $preset->expects($this->once())
//               ->method('getBinding')
//               ->with()
//               ->willReturn($binding);
        $attempt = $this->createAttemptWithPreset($preset);

        $status = new Status();

        $this->assertFalse($attempt->bind($status, $arguments));
        $this->assertFalse($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withSingleConnectionOption__invalidCredentialsException()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->createStub(LdapInterface::class);

        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));
        $binding = $this->bindingPresetMock(
            $ldap,
            $arguments,
            $this->throwException(new LdapException('Invalid Credentials', 0x31)),
            $this->returnValue('uid=jsmith,dc=foo')
        );

        $preset = $this->authAttemptPresetMock($this->returnValue([$connection]), $this->returnValue($binding));

        $attempt = $this->createAttemptWithPreset($preset);

        $status = new Status();

        $this->assertFalse($attempt->bind($status, $arguments));
        $this->assertFalse($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withSingleConnectionOption__recoverableException()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->createStub(LdapInterface::class);

        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));

        $binding = $this->bindingPresetMock(
            $ldap,
            $arguments,
            $this->throwException(new LdapException("can't connect to LDAP server", -1))
        );

        $preset = $this->authAttemptPresetMock($this->returnValue([$connection]), $this->returnValue($binding));

        $attempt = $this->createAttemptWithPreset($preset);

        $status = new Status();

        $this->assertFalse($attempt->bind($status, $arguments));
        $this->assertNull($status->getBindResult());
        $this->assertNull($status->getBindDn());
        $this->assertNull($status->getBindLdap());
        $this->assertNull($status->getBindConnection());
    }

    public function test__bind__withSingleConnectionOption__unrecoverableException()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap = $this->createStub(LdapInterface::class);

        $connection = $this->connectionPresetMock($arguments, $this->returnValue($ldap));

        $binding = $this->bindingPresetMock(
            $ldap,
            $arguments,
            $this->throwException(new LdapException("Invalid syntax", 0x15))
        );

        $preset = $this->authAttemptPresetMock($this->returnValue([$connection]), $this->returnValue($binding));

        $attempt = $this->createAttemptWithPreset($preset);

        $status = new Status();

        $this->expectException(LdapException::class);
        $this->expectExceptionMessage('Invalid syntax');
        $this->expectExceptionCode(0x15);

        $attempt->bind($status, $arguments);
    }

    public function test__bind__withDoubleConnectionOption_firstWorks()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap1 = $this->createStub(LdapInterface::class);

        $connection1 = $this->connectionPresetMock($arguments, $this->returnValue($ldap1));
        $connection2 = $this->createMock(ConnectionPreset::class);
        $connection2->expects($this->never())->method('createLdap');

        $binding = $this->bindingPresetMock($ldap1, $arguments, $this->returnValue(true), $this->returnValue('uid=jsmith,dc=foo'));
        $preset = $this->authAttemptPresetMock($this->returnValue([$connection1, $connection2]), $this->returnValue($binding));

        $attempt = $this->createAttemptWithPreset($preset);

        $status = new Status();

        $this->assertTrue($attempt->bind($status, $arguments));
        $this->assertTrue($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap1, $status->getBindLdap());
        $this->assertSame($connection1, $status->getBindConnection());
    }

    public function test__bind__withDoubleConnectionOption_firstFails()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $ldap1 = $this->createStub(LdapInterface::class);
        $ldap2 = $this->createStub(LdapInterface::class);

        $connection1 = $this->connectionPresetMock($arguments, $this->returnValue($ldap1));
        $connection2 = $this->connectionPresetMock($arguments, $this->returnValue($ldap2));

        $binding = $this->createMock(BindingPreset::class);
        $binding->expects($this->exactly(2))
                ->method('bindLdapInterface')
                ->withConsecutive([$this->identicalTo($ldap1), $arguments],
                                  [$this->identicalTo($ldap2), $arguments])
                ->will($this->returnCallback(function ($ldap, $arguments) use ($ldap1) {
                    if ($ldap === $ldap1) {
                        throw new LdapException("can't connect to LDAP server", -1);
                    } else {
                        return true;
                    }
                }));
        $binding->expects($this->once())
                ->method('getBindDn')
                ->with($arguments)
                ->willReturn('uid=jsmith,dc=foo');

        $preset = $this->authAttemptPresetMock($this->returnValue([$connection1, $connection2]), $this->returnValue($binding), 2);
        $attempt = $this->createAttemptWithPreset($preset);

        $status = new Status();

        $this->assertTrue($attempt->bind($status, $arguments));
        $this->assertTrue($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap2, $status->getBindLdap());
        $this->assertSame($connection2, $status->getBindConnection());
    }
}
