<?php

namespace Tests\Unit\Ldap\Auth;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Auth\Attempt;
use Cruftman\Ldap\Auth\Status;
use Cruftman\Ldap\Presets\Aggregate;
use Cruftman\Ldap\Presets\AuthAttempt as AuthAttemptPreset;
use Cruftman\Ldap\Presets\Binding as BindingPreset;
use Cruftman\Ldap\Presets\Connection as ConnectionPreset;
use Cruftman\Ldap\Presets\BindSearch as BindSearchPreset;
use Cruftman\Ldap\Traits\HasAuthAttemptPreset;
use Cruftman\Ldap\Traits\HasAuthStatus;
use Cruftman\Ldap\Traits\HasConnectorTool;
use Cruftman\Ldap\Traits\HasBinderTool;
use Cruftman\Ldap\Traits\HasFinderTool;
use Cruftman\Ldap\Tools\Connector;
use Cruftman\Ldap\Tools\Binder;
use Cruftman\Ldap\Tools\Finder;

use Korowai\Lib\Ldap\Ldap;
use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\EntryInterface;
use Korowai\Lib\Ldap\Exception\LdapException;
use Korowai\Lib\Ldap\Adapter\ResultInterface;

use Tests\Helpers\MockingHelper;
use Tests\Helpers\Ldap\LdapInterfaceMockingHelper;
use Tests\Helpers\Ldap\ToolsMockingHelper;

class AttemptTest extends TestCase
{
    use MockingHelper;
    use LdapInterfaceMockingHelper;
    use ToolsMockingHelper;

    //
    // data providers
    //
    public function boolProvider()
    {
        return [[true], [false]];
    }

    //
    // helpers
    //
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

    protected function createAttemptWithOptions(array $options, array $params)
    {
        $preset = $this->createAuthAttemptPreset($options, $params['presets'] ?? null);
        return $this->createAttemptWithPreset($preset, $params);
    }

    protected function createAttemptWithPreset(AuthAttemptPreset $preset, array $params)
    {
        $attempt = new Attempt($preset);
        $this->configureAttemptTools($attempt, $params);
        return $attempt;
    }

    protected function configureAttemptTools(Attempt $attempt, array $params)
    {
        $toolmap = [
            'connector' => ['ctor' => 'connectorToolMock',  'setter' => 'setConnector'],
            'binder'    => ['ctor' => 'binderToolMock',     'setter' => 'setBinder'],
            'finder'    => ['ctor' => 'finderToolMock',     'setter' => 'setFinder'],
        ];

        foreach ($toolmap as $key => $entry) {
            extract($entry);
            if (($obj = $params[$key] ?? null) !== null) {
                if (is_array($obj)) {
                    $obj = $this->{$ctor}($obj);
                }
                $attempt->{$setter}($obj);
            }
        }
    }

    //
    // Mock/Stub constructors
    //

    // LdapInterface
    protected function ldapInterfaceMock(array $config)
    {
        $ldap = $this->getMockBuilder(LdapInterface::class)->getMock();
        $this->configureLdapInterfaceMock($ldap, $config);
        return $ldap;
    }

    // Presets\Binding
    protected function bindingPresetMock(array $methodsConfig)
    {
        $binding = $this->createMock(BindingPreset::class);
        $methods = ['dn', 'password'];
        $this->configureMockMethods($binding, $methods, $methodsConfig);
        return $binding;
    }

    // Presets\AuthAttempt
    protected function authAttemptPresetMock(array $methodsConfig)
    {
        $preset = $this->createMock(AuthAttemptPreset::class);
        $methods = [
            'connections',
            'binding',
            'filtering',
            'fetching',
            'isSearchRequested',
            'getSearchIfRequested'
        ];
        $this->configureMockMethods($preset, $methods, $methodsConfig);
        return $preset;
    }

    // Tools\Connector
    protected function connectorToolMock(array $methodsConfig)
    {
        return $this->createConnectorMock(['methods' => $methodsConfig]);
    }

    // Tools\Binder
    protected function binderToolMock(array $methodsConfig)
    {
        return $this->createBinderMock(['methods' => $methodsConfig]);
    }

    // Tools\Finder
    protected function finderToolMock(array $methodsConfig)
    {
        return $this->createFinderMock(['methods' => $methodsConfig]);
    }

    public function test__uses__HasAuthAttemptPreset()
    {
        $uses = class_uses(Attempt::class);
        $this->assertContains(HasAuthAttemptPreset::class, $uses);
    }

    public function test__uses__HasAuthStatus()
    {
        $uses = class_uses(Attempt::class);
        $this->assertContains(HasAuthStatus::class, $uses);
    }

    public function test__uses__HasConnectorTool()
    {
        $uses = class_uses(Attempt::class);
        $this->assertContains(HasConnectorTool::class, $uses);
    }

    public function test__uses__HasBinderTool()
    {
        $uses = class_uses(Attempt::class);
        $this->assertContains(HasBinderTool::class, $uses);
    }

    public function test__uses__HasFinderTool()
    {
        $uses = class_uses(Attempt::class);
        $this->assertContains(HasFinderTool::class, $uses);
    }

    public function test__construct__withOneArg()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);

        $attempt = new Attempt($preset);
        $this->assertSame($preset, $attempt->getAuthAttemptPreset());
        $this->assertInstanceOf(Status::class, $attempt->getAuthStatus());
        $this->assertInstanceOf(Connector::class, $attempt->getConnector());
    }

    public function test__setConnector()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);
        $attempt = new Attempt($preset);
        $connector = $this->createStub(Connector::class);

        $this->assertSame($attempt, $attempt->setConnector($connector));
        $this->assertSame($connector, $attempt->getConnector());
    }

    public function test__setBinder()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);
        $attempt = new Attempt($preset);
        $binder = $this->createStub(Binder::class);

        $this->assertSame($attempt, $attempt->setBinder($binder));
        $this->assertSame($binder, $attempt->getBinder());
    }

    public function test__setFinder()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);
        $attempt = new Attempt($preset);
        $finder = $this->createStub(Finder::class);

        $this->assertSame($attempt, $attempt->setFinder($finder));
        $this->assertSame($finder, $attempt->getFinder());
    }

    public function test__setAuthStatus()
    {
        $preset = $this->createStub(AuthAttemptPreset::class);
        $status = new Status();

        $attempt = new Attempt($preset);

        $attempt->setAuthStatus($status);
        $this->assertSame($status, $attempt->getAuthStatus());

        $attempt->setAuthStatus(null);
        $newstat = $attempt->getAuthStatus();
        $this->assertInstanceOf(Status::class, $newstat);
        $this->assertNotSame($status, $newstat);
    }

    /**
     * @dataProvider boolProvider
     */
    public function test__bind__withConnectionArg(bool $expect)
    {
        $options = ['binding' => ['uid=${username},dc=foo', '${password}']];

        $ldap = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => ['times' => 1, 'with' => ['uid=jsmith,dc=foo', 'secret'], 'willReturn' => $expect],
                'createSearchQuery' => 'never',
            ]
        ]);

        $attempt = $this->createAttemptWithOptions(
            $options,
            ['connector' => ['createLdap' => ['times' => 1, 'willReturn' => $ldap]]]
        );

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);

        $status = $attempt->getAuthStatus();
        $this->assertSame($expect, $attempt->bind($arguments, $connection));
        $this->assertSame($expect, $status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
        $this->assertNull($status->getBindEntry());
    }

    public function test__bind__withConnectionArg__invalidCredentialsException()
    {
        $options = ['binding' => ['uid=${username},dc=foo', '${password}']];

        $ldap = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => [
                    'times' => 1,
                    'with'  => ['uid=jsmith,dc=foo', 'secret'],
                    'will'  => $this->throwException(new LdapException('Invalid Credentials', 0x31))
                ],
                'createSearchQuery' => 'never'
            ]
        ]);

        $attempt = $this->createAttemptWithOptions(
            $options,
            ['connector' => ['createLdap' => ['times' => 1, 'willReturn' => $ldap]]]
        );

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);

        $status = $attempt->getAuthStatus();

        $this->assertFalse($attempt->bind($arguments, $connection));
        $this->assertFalse($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
        $this->assertNull($status->getBindEntry());
    }

    public function test__bind__withConnectionArg__recoverableException()
    {
        $options = ['binding' => ['uid=${username},dc=foo', '${password}']];

        $ldap = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => [
                    'times' => 1,
                    'with'  => ['uid=jsmith,dc=foo', 'secret'],
                    'will'  => $this->throwException(new LdapException("can't connect to LDAP server", -1))
                ],
                'createSearchQuery' => 'never'
            ]
        ]);

        $attempt = $this->createAttemptWithOptions(
            $options,
            ['connector' => ['createLdap' => ['times' => 1, 'willReturn' => $ldap]]]
        );

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);

        $status = $attempt->getAuthStatus();
        $this->assertFalse($attempt->bind($arguments, $connection));
        $this->assertNull($status->getBindResult());
        $this->assertNull($status->getBindDn());
        $this->assertNull($status->getBindLdap());
        $this->assertNull($status->getBindConnection());
        $this->assertNull($status->getBindEntry());
    }

    public function test__bind__withConnectionArg__unrecoverableException()
    {
        $options = ['binding' => ['uid=${username},dc=foo', '${password}' ]];

        $ldap = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => [
                    'times' => 1,
                    'with'  => ['uid=jsmith,dc=foo', 'secret'],
                    'will'  => $this->throwException(new LdapException('Invalid syntax', 0x15))
                ],
                'createSearchQuery' => 'never'
            ]
        ]);

        $attempt = $this->createAttemptWithOptions(
            $options,
            ['connector' => ['createLdap' => ['times' => 1, 'willReturn' => $ldap]]]
        );

        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);

        $this->expectException(LdapException::class);
        $this->expectExceptionMessage('Invalid syntax');
        $this->expectExceptionCode(0x15);

        $attempt->bind($arguments, $connection);
    }

    public function test__bind__withMissingConnectionOption()
    {
        $preset = $this->authAttemptPresetMock([
            'connections' => ['times' => 1, 'willReturn' => null],
            'binding'     => 'never'
        ]);

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

        $binding = $this->bindingPresetMock([
            'dn'       => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'uid=jsmith,dc=foo'],
            'password' => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'secret']
        ]);
        $preset = $this->authAttemptPresetMock([
            'connections' => ['times' => 1, 'willReturn' => [$connection]],
            'binding'     => ['times' => 1, 'willReturn' => $binding]
        ]);
        $ldap = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => ['times' => 1, 'with' => ['uid=jsmith,dc=foo', 'secret'], 'willReturn' => $expect]
            ]
        ]);

        $attempt = $this->createAttemptWithPreset(
            $preset,
            ['connector' => ['createLdap' => ['times' => 1, 'willReturn' => $ldap]]]
        );

        $status = $attempt->getAuthStatus();

        $this->assertSame($expect, $attempt->bind($arguments));
        $this->assertSame($expect, $status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
        $this->assertNull($status->getBindEntry());
    }

    public function test__bind__withSingleConnectionOption__invalidCredentialsException()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);

        $binding = $this->bindingPresetMock([
            'dn'       => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'uid=jsmith,dc=foo'],
            'password' => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'secret']
        ]);

        $preset = $this->authAttemptPresetMock([
            'connections' => ['times' => 1, 'willReturn' => [$connection]],
            'binding'     => ['times' => 1, 'willReturn' => $binding]
        ]);

        $ldap = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => [
                    'times' => 1,
                    'with'  => ['uid=jsmith,dc=foo', 'secret'],
                    'will'  => $this->throwException(new LdapException('Invalid Credentials', 0x31))
                ]
            ]
        ]);

        $attempt = $this->createAttemptWithPreset(
            $preset,
            ['connector' => ['createLdap' => ['times' => 1, 'willReturn' => $ldap]]]
        );

        $status = $attempt->getAuthStatus();

        $this->assertFalse($attempt->bind($arguments));
        $this->assertFalse($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
        $this->assertNull($status->getBindEntry());
    }

    public function test__bind__withSingleConnectionOption__recoverableException()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);

        $binding = $this->bindingPresetMock([
            'dn'       => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'uid=jsmith,dc=foo'],
            'password' => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'secret']
        ]);
        $preset = $this->authAttemptPresetMock([
            'connections' => ['times' => 1, 'willReturn' => [$connection]],
            'binding'     => ['times' => 1, 'willReturn' => $binding]
        ]);
        $ldap = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => [
                    'times' => 1,
                    'with'  => ['uid=jsmith,dc=foo', 'secret'],
                    'will'  => $this->throwException(new LdapException("can't connect to LDAP server", -1))
                ]
            ]
        ]);

        $attempt = $this->createAttemptWithPreset(
            $preset,
            ['connector' => ['createLdap' => ['times' => 1, 'willReturn' => $ldap]]]
        );

        $status = $attempt->getAuthStatus();

        $this->assertFalse($attempt->bind($arguments));
        $this->assertNull($status->getBindResult());
        $this->assertNull($status->getBindDn());
        $this->assertNull($status->getBindLdap());
        $this->assertNull($status->getBindConnection());
        $this->assertNull($status->getBindEntry());
    }

    public function test__bind__withSingleConnectionOption__unrecoverableException()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];
        $connection = $this->createMock(ConnectionPreset::class);

        $binding = $this->bindingPresetMock([
            'dn'       => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'uid=jsmith,dc=foo'],
            'password' => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'secret']
        ]);
        $preset = $this->authAttemptPresetMock([
            'connections' => ['times' => 1, 'willReturn' => [$connection]],
            'binding'     => ['times' => 1, 'willReturn' => $binding]
        ]);
        $ldap = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => [
                    'times' => 1,
                    'with'  => ['uid=jsmith,dc=foo', 'secret'],
                    'will'  => $this->throwException(new LdapException('Invalid syntax', 0x15))
                ]
            ]
        ]);

        $attempt = $this->createAttemptWithPreset(
            $preset,
            ['connector' => ['createLdap' => ['times' => 1, 'willReturn' => $ldap]]]
        );

        $status = $attempt->getAuthStatus();

        $this->expectException(LdapException::class);
        $this->expectExceptionMessage('Invalid syntax');
        $this->expectExceptionCode(0x15);

        $attempt->bind($arguments);
    }

    public function test__bind__withDoubleConnectionOption_firstWorks()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $connection1 = $this->createMock(ConnectionPreset::class);
        $connection2 = $this->createMock(ConnectionPreset::class);

        $binding = $this->bindingPresetMock([
            'dn'       => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'uid=jsmith,dc=foo'],
            'password' => ['times' => 1, 'with' => [$arguments], 'willReturn' => 'secret']
        ]);
        $preset = $this->authAttemptPresetMock([
            'connections' => ['times' => 1, 'willReturn' => [$connection1, $connection2]],
            'binding'     => ['times' => 1, 'willReturn' => $binding]
        ]);

        $ldap1 = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => ['times' => 1, 'with' => ['uid=jsmith,dc=foo', 'secret'], 'willReturn' => true]
            ]
        ]);
        $attempt = $this->createAttemptWithPreset(
            $preset,
            ['connector' => ['createLdap' => ['times' => 1, 'willReturn' => $ldap1]]]
        );

        $status = $attempt->getAuthStatus();

        $this->assertTrue($attempt->bind($arguments));
        $this->assertTrue($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap1, $status->getBindLdap());
        $this->assertSame($connection1, $status->getBindConnection());
        $this->assertNull($status->getBindEntry());
    }

    public function test__bind__withDoubleConnectionOption_firstFails()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $connection1 = $this->createMock(ConnectionPreset::class);
        $connection2 = $this->createMock(ConnectionPreset::class);

        $binding = $this->bindingPresetMock([
            'dn'       => ['times' => 2, 'with' => [$arguments], 'willReturn' => 'uid=jsmith,dc=foo'],
            'password' => ['times' => 2, 'with' => [$arguments], 'willReturn' => 'secret']
        ]);

        $preset = $this->authAttemptPresetMock([
            'connections' => ['times' => 1, 'willReturn' => [$connection1, $connection2]],
            'binding'     => ['times' => 2, 'willReturn' => $binding],
        ]);

        $ldap1 = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => [
                    'times' => 1,
                    'with'  => ['uid=jsmith,dc=foo', 'secret'],
                    'will'  => $this->throwException(new LdapException("can't connect to LDAP server", -1))
                ]
            ]
        ]);

        $ldap2 = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => ['times' => 1, 'with' => ['uid=jsmith,dc=foo', 'secret'], 'willReturn' => true]
            ]
        ]);

        $connector = $this->connectorToolMock([
            'createLdap' => [
                'times'           => 2,
                'withConsecutive' => [[$connection1, $arguments], [$connection2, $arguments]],
                'will'            => $this->onConsecutiveCalls($ldap1, $ldap2)
            ]
        ]);

        $attempt = $this->createAttemptWithPreset($preset, ['connector' => $connector]);

        $status = $attempt->getAuthStatus();

        $this->assertTrue($attempt->bind($arguments));
        $this->assertTrue($status->getBindResult());
        $this->assertSame('uid=jsmith,dc=foo', $status->getBindDn());
        $this->assertSame($ldap2, $status->getBindLdap());
        $this->assertSame($connection2, $status->getBindConnection());
        $this->assertNull($status->getBindEntry());
    }

    public function test__bind__withDoubleConnectionOption_bothFail()
    {
        $arguments = ['username' => 'jsmith', 'password' => 'secret'];

        $connection1 = $this->createMock(ConnectionPreset::class);
        $connection2 = $this->createMock(ConnectionPreset::class);

        $binding = $this->bindingPresetMock([
            'dn'       => ['times' => 2, 'with' => [$arguments], 'willReturn' => 'uid=jsmith,dc=foo'],
            'password' => ['times' => 2, 'with' => [$arguments], 'willReturn' => 'secret']
        ]);

        $preset = $this->authAttemptPresetMock([
            'connections' => ['times' => 1, 'willReturn' => [$connection1, $connection2]],
            'binding'     => ['times' => 2, 'willReturn' => $binding],
        ]);

        $ldap1 = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => [
                    'times' => 1,
                    'with'  => ['uid=jsmith,dc=foo', 'secret'],
                    'will'  => $this->throwException(new LdapException("can't connect to LDAP server", -1))
                ]
            ]
        ]);

        $ldap2 = $this->ldapInterfaceMock([
            'methods' => [
                'bind' => [
                    'times' => 1,
                    'with'  => ['uid=jsmith,dc=foo', 'secret'],
                    'will'  => $this->throwException(new LdapException("can't connect to LDAP server", -1))
                ]
            ]
        ]);

        $connector = $this->connectorToolMock([
            'createLdap' => [
                'times'           => 2,
                'withConsecutive' => [[$connection1, $arguments], [$connection2, $arguments]],
                'will'            => $this->onConsecutiveCalls($ldap1, $ldap2)
            ]
        ]);

        $attempt = $this->createAttemptWithPreset($preset, ['connector' => $connector]);

        $status = $attempt->getAuthStatus();

        $this->assertFalse($attempt->bind($arguments));
        $this->assertNull($status->getBindResult());
        $this->assertNull($status->getBindDn());
        $this->assertNull($status->getBindLdap());
        $this->assertNull($status->getBindConnection());
        $this->assertNull($status->getBindEntry());
    }
}
