<?php

namespace Tests\Unit\Ldap\Auth;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Auth\Attempt;
use Cruftman\Ldap\Auth\Status;
use Cruftman\Ldap\Presets\Aggregate;
use Cruftman\Ldap\Presets\AuthAttempt as AuthAttemptPreset;
use Cruftman\Ldap\Presets\Binding as BindingPreset;
use Cruftman\Ldap\Traits\HasAuthAttemptPreset;

use Korowai\Lib\Ldap\Ldap;
use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\Exception\LdapException;
use Cruftman\Ldap\Presets\Connection as ConnectionPreset;

class AttemptTest extends TestCase
{
    protected function createPresetsAggregate(array $options = null)
    {
        if ($options === null) {
            $options = ['auth_schema' => []];
        }
        return new Aggregate($options);
    }

    protected function createAuthAttemptPreset(array $options, ?Aggregate $presets = null)
    {
        if ($presets === null) {
            $presets = $this->createPresetsAggregate();
        }
        return $presets->authAttempt($options);
    }

    protected function createAttemptWithOptions(array $options, $ldapMock = null, ?Aggregate $presets = null)
    {
        $preset = $this->createAuthAttemptPreset($options, $presets);
        return $this->createAttemptWithPreset($preset, $ldapMock);
    }

    protected function createAttemptWithPreset(AuthAttemptPreset $preset, $ldapMock = null)
    {
        $ctor = $ldapMock ? function (array $options = []) use ($ldapMock) { return $ldapMock; } : null;
        return new Attempt($preset, null, $ctor);
    }

    protected function ldapInterfaceMock(array $bindWith, $bindWill)
    {
        $ldap = $this->getMockBuilder(LdapInterface::class)->getMock();
        $ldap->expects($this->once())
             ->method('bind')
             ->with(...$bindWith)
             ->will($bindWill);
        return $ldap;
    }

    public function boolProvider()
    {
        return [[true], [false]];
    }

    protected function bindingPresetMock($arguments, $dnWill = null, $pwWill = null, $iterations = 1)
    {
        $binding = $this->createMock(BindingPreset::class);
        if ($dnWill === null) {
            $binding->expects($this->never())
                    ->method('dn');
        } else {
            $binding->expects($this->exactly($iterations))
                    ->method('dn')
                    ->with($arguments)
                    ->will($dnWill);
        }
        if ($pwWill === null) {
            $binding->expects($this->never())
                    ->method('password');
        } else {
            $binding->expects($this->exactly($iterations))
                    ->method('password')
                    ->with($arguments)
                    ->will($pwWill);
        }
        return $binding;
    }

    protected function authAttemptPresetMock($connectionsWill = null, $bindingWill = null, $iterations = 1)
    {
        $preset = $this->createMock(AuthAttemptPreset::class);
        if ($connectionsWill === null) {
            $preset->expects($this->never())
                   ->method('connections');
        } else {
            $preset->expects($this->once())
                   ->method('connections')
                   ->with()
                   ->will($connectionsWill);
        }

        if ($bindingWill === null) {
            $preset->expects($this->never())
                   ->method('binding');
        } else {
            $preset->expects($this->exactly($iterations))
                   ->method('binding')
                   ->with()
                   ->will($bindingWill);
        }
        return $preset;
    }

    public function test__uses__HasAuthAttemptPreset()
    {
        $uses = class_uses(Attempt::class);
        $this->assertContains(HasAuthAttemptPreset::class, $uses);
    }

    public function test__construct__withOneArg()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);

        $attempt = new Attempt($preset);
        $this->assertSame($preset, $attempt->getAuthAttemptPreset());
        $this->assertInstanceOf(Status::class, $attempt->getStatus());
        $this->assertSame([Ldap::class, 'createWithConfig'], $attempt->getLdapConstructor());
    }

    public function test__construct__withTwoArgs()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);
        $status = new Status();

        $attempt = new Attempt($preset, $status);
        $this->assertSame($preset, $attempt->getAuthAttemptPreset());
        $this->assertSame($status, $attempt->getStatus());
        $this->assertSame([Ldap::class, 'createWithConfig'], $attempt->getLdapConstructor());

        $attempt = new Attempt($preset, null);
        $this->assertSame($preset, $attempt->getAuthAttemptPreset());
        $this->assertInstanceOf(Status::class, $attempt->getStatus());
        $this->assertSame([Ldap::class, 'createWithConfig'], $attempt->getLdapConstructor());
    }

    public function test__construct__withThreeArgs()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);
        $status = new Status();
        $ctor = function ($config) {};

        $attempt = new Attempt($preset, $status, $ctor);
        $this->assertSame($preset, $attempt->getAuthAttemptPreset());
        $this->assertSame($status, $attempt->getStatus());
        $this->assertSame($ctor, $attempt->getLdapConstructor());

        $attempt = new Attempt($preset, null, null);
        $this->assertSame($preset, $attempt->getAuthAttemptPreset());
        $this->assertInstanceOf(Status::class, $attempt->getStatus());
        $this->assertSame([Ldap::class, 'createWithConfig'], $attempt->getLdapConstructor());
    }

    public function test__setStatus()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);
        $status = new Status();

        $attempt = new Attempt($preset);

        $attempt->setStatus($status);
        $this->assertSame($status, $attempt->getStatus());

        $attempt->setStatus(null);
        $newstat = $attempt->getStatus();
        $this->assertInstanceOf(Status::class, $newstat);
        $this->assertNotSame($status, $newstat);

        $attempt->setStatus();
        $newstat2 = $attempt->getStatus();
        $this->assertInstanceOf(Status::class, $newstat2);
        $this->assertNotSame($status, $newstat2);
        $this->assertNotSame($newstat, $newstat2);
    }

    public function test__setLdapConstructor()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);
        $ctor = function () {};

        $attempt = new Attempt($preset);

        $attempt->setLdapConstructor($ctor);
        $this->assertSame($ctor, $attempt->getLdapConstructor());

        $attempt->setLdapConstructor(null);
        $this->assertSame([Ldap::class, 'createWithConfig'], $attempt->getLdapConstructor());

        $attempt->setLdapConstructor();
        $this->assertSame([Ldap::class, 'createWithConfig'], $attempt->getLdapConstructor());
    }

    /**
     * @dataProvider boolProvider
     */
    public function test__bind__withConnectionArg(bool $expect)
    {
        $options = ['binding' => ['uid=${username},dc=foo', '${password}']];
        $ldap = $this->ldapInterfaceMock(['uid=jsmith,dc=foo', 'secret'], $this->returnValue($expect));
        $attempt = $this->createAttemptWithOptions($options, $ldap);

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);
        $connection->expects($this->once())
                   ->method('ldapConfig')
                   ->with($arguments)
                   ->will($this->returnValue([]));

        $status = $attempt->getStatus();
        $this->assertSame($expect, $attempt->bind($arguments, $connection));
        $this->assertSame($expect, $status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withConnectionArg__invalidCredentialsException()
    {
        $options = ['binding' => ['uid=${username},dc=foo', '${password}']];
        $ldap = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->throwException(new LdapException('Invalid Credentials', 0x31))
        );
        $attempt = $this->createAttemptWithOptions($options, $ldap);

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);
        $connection->expects($this->once())
                   ->method('ldapConfig')
                   ->with($arguments)
                   ->will($this->returnValue([]));

        $status = $attempt->getStatus();

        $this->assertFalse($attempt->bind($arguments, $connection));
        $this->assertFalse($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap()); $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withConnectionArg__recoverableException()
    {
        $options = ['binding' => ['uid=${username},dc=foo', '${password}']];
        $ldap = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->throwException(new LdapException("can't connect to LDAP server", -1))
        );
        $attempt = $this->createAttemptWithOptions($options, $ldap);

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);
        $connection->expects($this->once())
                   ->method('ldapConfig')
                   ->with($arguments)
                   ->will($this->returnValue([]));

        $status = $attempt->getStatus();
        $this->assertFalse($attempt->bind($arguments, $connection));
        $this->assertNull($status->getBindResult());
        $this->assertNull($status->getBindDn());
        $this->assertNull($status->getBindLdap());
        $this->assertNull($status->getBindConnection());
    }

    public function test__bind__withConnectionArg__unrecoverableException()
    {
        $options = ['binding' => ['uid=${username},dc=foo', '${password}' ]];
        $ldap = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->throwException(new LdapException('Invalid syntax', 0x15))
        );
        $attempt = $this->createAttemptWithOptions($options, $ldap);

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);
        $connection->expects($this->once())
                   ->method('ldapConfig')
                   ->with($arguments)
                   ->will($this->returnValue([]));

        $this->expectException(LdapException::class);
        $this->expectExceptionMessage('Invalid syntax');
        $this->expectExceptionCode(0x15);

        $attempt->bind($arguments, $connection);
    }

    public function test__bind__withMissingConnectionOption()
    {
        $preset = $this->authAttemptPresetMock($this->returnValue(null));

        $attempt = new Attempt($preset);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing "connections" in AuthAttempt preset, check your config.');
        $attempt->bind([]);
    }

    /**
     * @dataProvider boolProvider
     */
    public function test__bind__withSingleConnectionOption(bool $expect)
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);
        $connection->expects($this->once())
                   ->method('ldapConfig')
                   ->with($arguments)
                   ->will($this->returnValue([]));

        $binding = $this->bindingPresetMock(
            $arguments,
            $this->returnValue('uid=jsmith,dc=foo'),
            $this->returnValue('secret')
        );
        $preset = $this->authAttemptPresetMock(
            $this->returnValue([$connection]),
            $this->returnValue($binding)
        );
        $ldap = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->returnValue($expect)
        );

        $attempt = $this->createAttemptWithPreset($preset, $ldap);

        $status = $attempt->getStatus();

        $this->assertSame($expect, $attempt->bind($arguments));
        $this->assertSame($expect, $status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withSingleConnectionOption__invalidCredentialsException()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);
        $connection->expects($this->once())
                   ->method('ldapConfig')
                   ->with($arguments)
                   ->will($this->returnValue([]));

        $binding = $this->bindingPresetMock(
            $arguments,
            $this->returnValue('uid=jsmith,dc=foo'),
            $this->returnValue('secret')
        );
        $preset = $this->authAttemptPresetMock(
            $this->returnValue([$connection]),
            $this->returnValue($binding)
        );
        $ldap = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->throwException(new LdapException('Invalid Credentials', 0x31))
        );

        $attempt = $this->createAttemptWithPreset($preset, $ldap);

        $status = $attempt->getStatus();

        $this->assertFalse($attempt->bind($arguments));
        $this->assertFalse($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
    }

    public function test__bind__withSingleConnectionOption__recoverableException()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);
        $connection->expects($this->once())
                   ->method('ldapConfig')
                   ->with($arguments)
                   ->will($this->returnValue([]));

        $binding = $this->bindingPresetMock(
            $arguments,
            $this->returnValue('uid=jsmith,dc=foo'),
            $this->returnValue('secret')
        );
        $preset = $this->authAttemptPresetMock(
            $this->returnValue([$connection]),
            $this->returnValue($binding)
        );
        $ldap = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->throwException(new LdapException("can't connect to LDAP server", -1))
        );

        $attempt = $this->createAttemptWithPreset($preset, $ldap);

        $status = $attempt->getStatus();

        $this->assertFalse($attempt->bind($arguments));
        $this->assertNull($status->getBindResult());
        $this->assertNull($status->getBindDn());
        $this->assertNull($status->getBindLdap());
        $this->assertNull($status->getBindConnection());
    }

    public function test__bind__withSingleConnectionOption__unrecoverableException()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);
        $connection->expects($this->once())
                   ->method('ldapConfig')
                   ->with($arguments)
                   ->will($this->returnValue([]));

        $binding = $this->bindingPresetMock(
            $arguments,
            $this->returnValue('uid=jsmith,dc=foo'),
            $this->returnValue('secret')
        );
        $preset = $this->authAttemptPresetMock(
            $this->returnValue([$connection]),
            $this->returnValue($binding)
        );
        $ldap = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->throwException(new LdapException('Invalid syntax', 0x15))
        );

        $attempt = $this->createAttemptWithPreset($preset, $ldap);

        $status = $attempt->getStatus();

        $this->expectException(LdapException::class);
        $this->expectExceptionMessage('Invalid syntax');
        $this->expectExceptionCode(0x15);

        $attempt->bind($arguments);
    }

    public function test__bind__withDoubleConnectionOption_firstWorks()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $connection1 = $this->createMock(ConnectionPreset::class);
        $connection1->expects($this->once())
                    ->method('ldapConfig')
                    ->with($arguments)
                    ->will($this->returnValue(['CONN1']));
        $connection2 = $this->createMock(ConnectionPreset::class);
        $connection2->expects($this->never())
                    ->method('ldapConfig');

        $binding = $this->bindingPresetMock(
            $arguments,
            $this->returnValue('uid=jsmith,dc=foo'),
            $this->returnValue('secret')
        );
        $preset = $this->authAttemptPresetMock(
            $this->returnValue([$connection1, $connection2]),
            $this->returnValue($binding)
        );

        $ldap1 = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->returnValue(true)
        );
        $attempt = $this->createAttemptWithPreset($preset, $ldap1);

        $status = $attempt->getStatus();

        $this->assertTrue($attempt->bind($arguments));
        $this->assertTrue($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap1, $status->getBindLdap());
        $this->assertSame($connection1, $status->getBindConnection());
    }

    public function test__bind__withDoubleConnectionOption_firstFails()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $connection1 = $this->createMock(ConnectionPreset::class);
        $connection1->expects($this->once())
                    ->method('ldapConfig')
                    ->with($arguments)
                    ->will($this->returnValue(['CONN1']));
        $connection2 = $this->createMock(ConnectionPreset::class);
        $connection2->expects($this->once())
                    ->method('ldapConfig')
                    ->with($arguments)
                    ->will($this->returnValue(['CONN2']));

        $binding = $this->bindingPresetMock(
            $arguments,
            $this->returnValue('uid=jsmith,dc=foo'),
            $this->returnValue('secret'),
            2
        );
        $preset = $this->authAttemptPresetMock(
            $this->returnValue([$connection1, $connection2]),
            $this->returnValue($binding),
            2
        );

        $ldap1 = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->throwException(new LdapException("can't connect to LDAP server", -1))
        );
        $ldap2 = $this->ldapInterfaceMock(
            ['uid=jsmith,dc=foo', 'secret'],
            $this->returnValue(true)
        );
        $attempt = $this->createAttemptWithPreset($preset);
        $attempt->setLdapConstructor(function (array $options) use ($ldap1, $ldap2) {
            if ($options == ['CONN1']) {
                return $ldap1;
            } else {
                return $ldap2;
            }
        });

        $status = $attempt->getStatus();

        $this->assertTrue($attempt->bind($arguments));
        $this->assertTrue($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap2, $status->getBindLdap());
        $this->assertSame($connection2, $status->getBindConnection());
    }
}
