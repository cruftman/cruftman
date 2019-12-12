<?php

namespace Tests\Unit\Ldap\Auth;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Auth\Schema;
use Cruftman\Ldap\Auth\Source;
//use Cruftman\Ldap\Auth\Attempt;
use Cruftman\Ldap\Auth\Status;
//use Cruftman\Ldap\Auth\Entry;
use Cruftman\Ldap\Traits\HasAuthSchemaPreset;
use Cruftman\Ldap\Traits\HasAuthStatus;
use Cruftman\Ldap\Traits\HasConnectorTool;
use Cruftman\Ldap\Traits\HasBinderTool;
use Cruftman\Ldap\Traits\HasFinderTool;
//
use Cruftman\Ldap\Presets\AuthSchema;
use Cruftman\Ldap\Presets\AuthSource;
use Cruftman\Ldap\Presets\Search;
//use Cruftman\Ldap\Presets\Session;
use Cruftman\Ldap\Tools\Connector;
use Cruftman\Ldap\Tools\Binder;
use Cruftman\Ldap\Tools\Finder;
//
//use Korowai\Lib\Ldap\LdapInterface;
//use Korowai\Lib\Ldap\EntryInterface;
//use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
//use Korowai\Lib\Ldap\Adapter\ResultInterface;
//use Korowai\Lib\Ldap\Exception\LdapException;

class SchemaTest extends TestCase
{
    public function test__uses__HasAuthSchemaPreset()
    {
        $uses = class_uses(Schema::class);
        $this->assertContains(HasAuthSchemaPreset::class, $uses);
    }

    public function test__uses__HasAuthStatus()
    {
        $uses = class_uses(Schema::class);
        $this->assertContains(HasAuthStatus::class, $uses);
    }

    public function test__uses__HasConnectorTool()
    {
        $uses = class_uses(Schema::class);
        $this->assertContains(HasConnectorTool::class, $uses);
    }

    public function test__uses__HasBinderTool()
    {
        $uses = class_uses(Schema::class);
        $this->assertContains(HasBinderTool::class, $uses);
    }

    public function test__uses__HasFinderTool()
    {
        $uses = class_uses(Schema::class);
        $this->assertContains(HasFinderTool::class, $uses);
    }

    public function test__construct()
    {
        $preset = $this->createStub(AuthSchema::class);
        $schema = new Schema($preset);
        $this->assertSame($preset, $schema->getAuthSchemaPreset());
    }

    public function test__setSources()
    {
        $sourcePresets = [$this->createStub(AuthSource::class), $this->createStub(AuthSource::class)];
        $preset = $this->createStub(AuthSchema::class);
        $preset->expects($this->any())
               ->method('sources')
               ->with()
               ->will($this->returnValue($sourcePresets));

        $schema = new Schema($preset);

        $sources = $schema->getSources();
        $this->assertIsArray($sources);
        $this->assertSame($sources, $schema->getSources());
        $this->assertCount(2, $sources);
        $this->assertInstanceOf(Source::class, $sources[0]);
        $this->assertInstanceOf(Source::class, $sources[1]);
        $this->assertSame($sourcePresets[0], $sources[0]->getAuthSourcePreset());
        $this->assertSame($sourcePresets[1], $sources[1]->getAuthSourcePreset());

        $sources = [$this->createStub(Source::class)];
        $this->assertSame($schema, $schema->setSources($sources));
        $this->assertSame($sources, $schema->getSources());

        $this->assertSame($schema, $schema->setSources(null));
        $sources = $schema->getSources();
        $this->assertIsArray($sources);
        $this->assertSame($sources, $schema->getSources());
        $this->assertCount(2, $sources);
        $this->assertInstanceOf(Source::class, $sources[0]);
        $this->assertInstanceOf(Source::class, $sources[1]);
        $this->assertSame($sourcePresets[0], $sources[0]->getAuthSourcePreset());
        $this->assertSame($sourcePresets[1], $sources[1]->getAuthSourcePreset());
    }

    public function test__sources__inheritedAttributes()
    {
        $sourcePresets = [$this->createStub(AuthSource::class), $this->createStub(AuthSource::class)];
        $preset = $this->createStub(AuthSchema::class);
        $preset->expects($this->any())
               ->method('sources')
               ->with()
               ->will($this->returnValue($sourcePresets));

        $schema = new Schema($preset);

        $status = $this->createStub(Status::class);
        $connector = $this->createStub(Connector::class);
        $binder = $this->createStub(Binder::class);
        $finder = $this->createStub(Finder::class);
        $schema->setAuthStatus($status)
               ->setConnector($connector)
               ->setBinder($binder)
               ->setFinder($finder);

        $sources = $schema->getSources();
        $this->assertSame($sources, $schema->getSources());
        $this->assertIsArray($sources);
        $this->assertCount(2, $sources);
        $this->assertNotSame($status, $sources[0]->getAuthStatus());
        $this->assertNotSame($status, $sources[1]->getAuthStatus());
        $this->assertSame($connector, $sources[0]->getConnector());
        $this->assertSame($connector, $sources[1]->getConnector());
        $this->assertSame($binder, $sources[0]->getBinder());
        $this->assertSame($binder, $sources[1]->getBinder());
        $this->assertSame($finder, $sources[0]->getFinder());
        $this->assertSame($finder, $sources[1]->getFinder());
    }

    public function test__attempt()
    {
        $preset = $this->createStub(AuthSchema::class);
        $schema = new Schema($preset);
        $this->assertIsCallable([$schema, 'attempt']);
        $this->markTestIncomplete("Test not implemented yet");
    }

    public function test__search()
    {
        $preset = $this->createStub(AuthSchema::class);

        $entries0 = ['e0'];
        $entries1 = ['e1'];

        $arguments = ['foo' => 'FOO'];

        $sources = [$this->createStub(Source::class), $this->createStub(Source::class)];
        $sources[0]->expects($this->once())
                   ->method('search')
                   ->with($arguments)
                   ->will($this->returnValue($entries0));
        $sources[1]->expects($this->once())
                   ->method('search')
                   ->with($arguments)
                   ->will($this->returnValue($entries1));

        $schema = (new Schema($preset))->setSources($sources);

        $this->assertSame(['e0', 'e1'], $schema->search($arguments));
    }

    public function test__locate()
    {
        $preset = $this->createStub(AuthSchema::class);

        $entries0 = ['e0'];
        $entries1 = ['e1'];

        $arguments = ['foo' => 'FOO'];

        $sources = [$this->createStub(Source::class), $this->createStub(Source::class)];
        $sources[0]->expects($this->once())
                   ->method('locate')
                   ->with($arguments)
                   ->will($this->returnValue($entries0));
        $sources[1]->expects($this->once())
                   ->method('locate')
                   ->with($arguments)
                   ->will($this->returnValue($entries1));

        $schema = (new Schema($preset))->setSources($sources);

        $this->assertSame(['e0', 'e1'], $schema->locate($arguments));
    }
}
