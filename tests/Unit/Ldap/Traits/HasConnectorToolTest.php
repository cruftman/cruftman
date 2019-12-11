<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasConnectorTool;
use Cruftman\Ldap\Tools\Connector;

class HasConnectorToolTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasConnectorTool;
        };

        $this->assertInstanceOf(Connector::class, $object->getConnector());

        $stub = $this->createStub(Connector::class);
        $this->assertSame($object, $object->setConnector($stub));

        $this->assertSame($stub, $object->getConnector());

        $this->assertSame($object, $object->setConnector(null));
        $this->assertInstanceOf(Connector::class, $object->getConnector());
    }
}
