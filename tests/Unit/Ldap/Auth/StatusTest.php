<?php

namespace Tests\Unit\Ldap\Auth;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Auth\Status;
use Cruftman\Ldap\Auth\Source;
use Korowai\Lib\Ldap\LdapInterface;
use Cruftman\Ldap\Preset\Connection;

class StatusTest extends TestCase
{
    public function test__construct()
    {
        $status = new Status();
        $this->assertNull($status->getBindResult());
        $this->assertNull($status->getBindDn());
        $this->assertNull($status->getBindLdap());
        $this->assertNull($status->getBindConnection());
        $this->assertNull($status->getSource());
    }

    public function test__construct__withOptions()
    {
        $ldap = $this->createStub(LdapInterface::class);
        $connection = $this->createStub(Connection::class);
        $source = $this->createStub(Source::class);

        $status = new Status([
            'bindResult' => true,
            'bindDn' => 'uid=jsmith,ou=people,dc=example,dc=org',
            'bindLdap' => $ldap,
            'bindConnection' => $connection,
            'source' => $source
        ]);

        $this->assertTrue($status->getBindResult());
        $this->assertSame('uid=jsmith,ou=people,dc=example,dc=org', $status->getBindDn());
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($connection, $status->getBindConnection());
        $this->assertSame($source, $status->getSource());
    }

    public function test__bindResult()
    {
        $status = new Status();
        $this->assertNull($status->getBindResult());
        $this->assertSame($status, $status->setBindResult(false));
        $this->assertFalse($status->getBindResult());
        $this->assertSame($status, $status->setBindResult(true));
        $this->assertTrue($status->getBindResult());
        $this->assertSame($status, $status->setBindResult(null));
        $this->assertNull($status->getBindResult());
    }

    public function test__bindDn()
    {
        $status = new Status();
        $this->assertNull($status->getBindDn());
        $this->assertSame($status, $status->setBindDn('uid=jsmith,ou=people,dc=example,dc=org'));
        $this->assertSame('uid=jsmith,ou=people,dc=example,dc=org', $status->getBindDn());
        $this->assertSame($status, $status->setBindDn(null));
        $this->assertNull($status->getBindDn());
    }

    public function test__bindLdap()
    {
        $status = new Status();
        $ldap = $this->createStub(LdapInterface::class);

        $this->assertNull($status->getBindLdap());
        $this->assertSame($status, $status->setBindLdap($ldap));
        $this->assertSame($ldap, $status->getBindLdap());
        $this->assertSame($status, $status->setBindLdap(null));
        $this->assertNull($status->getBindLdap());
    }

    public function test__bindConnection()
    {
        $status = new Status();
        $connection = $this->createStub(Connection::class);
        $this->assertNull($status->getBindConnection());
        $this->assertSame($status, $status->setBindConnection($connection));
        $this->assertSame($connection, $status->getBindConnection());
        $this->assertSame($status, $status->setBindConnection(null));
        $this->assertNull($status->getBindConnection());
    }

    public function test__source()
    {
        $status = new Status();
        $source = $this->createStub(Source::class);
        $this->assertNull($status->getSource());
        $this->assertSame($status, $status->setSource($source));
        $this->assertSame($source, $status->getSource());
        $this->assertSame($status, $status->setSource(null));
        $this->assertNull($status->getSource());
    }

    public function test__resetBindStatus()
    {
        $ldap = $this->createStub(LdapInterface::class);
        $connection = $this->createStub(Connection::class);
        $source = $this->createStub(Source::class);

        $status = new Status([
            'bindResult' => true,
            'bindDn' => 'uid=jsmith,ou=people,dc=example,dc=org',
            'bindLdap' => $ldap,
            'bindConnection' => $connection,
            'source' => $source
        ]);

        $status->resetBindStatus();

        $this->assertNull($status->getBindResult());
        $this->assertNull($status->getBindDn());
        $this->assertNull($status->getBindLdap());
        $this->assertNull($status->getBindConnection());
        $this->assertSame($source, $status->getSource());
    }
}
