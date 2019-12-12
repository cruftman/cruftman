<?php

namespace Tests\Unit\Ldap\Auth;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Auth\Source;
use Cruftman\Ldap\Auth\Attempt;
use Cruftman\Ldap\Auth\Status;
use Cruftman\Ldap\Auth\Entry;
use Cruftman\Ldap\Traits\HasAuthSourcePreset;
use Cruftman\Ldap\Traits\HasAuthStatus;
use Cruftman\Ldap\Traits\HasConnectorTool;
use Cruftman\Ldap\Traits\HasBinderTool;
use Cruftman\Ldap\Traits\HasFinderTool;

use Cruftman\Ldap\Presets\AuthSource;
use Cruftman\Ldap\Presets\Search;
use Cruftman\Ldap\Presets\Session;
use Cruftman\Ldap\Tools\Connector;
use Cruftman\Ldap\Tools\Binder;
use Cruftman\Ldap\Tools\Finder;

use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\EntryInterface;
use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\ResultInterface;
use Korowai\Lib\Ldap\Exception\LdapException;

class SourceTest extends TestCase
{
    public function test__uses__HasAuthSourcePreset()
    {
        $uses = class_uses(Source::class);
        $this->assertContains(HasAuthSourcePreset::class, $uses);
    }

    public function test__uses__HasAuthStatus()
    {
        $uses = class_uses(Source::class);
        $this->assertContains(HasAuthStatus::class, $uses);
    }

    public function test__uses__HasConnectorTool()
    {
        $uses = class_uses(Source::class);
        $this->assertContains(HasConnectorTool::class, $uses);
    }

    public function test__uses__HasBinderTool()
    {
        $uses = class_uses(Source::class);
        $this->assertContains(HasBinderTool::class, $uses);
    }

    public function test__uses__HasFinderTool()
    {
        $uses = class_uses(Source::class);
        $this->assertContains(HasFinderTool::class, $uses);
    }

    public function test__construct()
    {
        $preset = $this->createStub(AuthSource::class);
        $source = new Source($preset);
        $this->assertSame($preset, $source->getAuthSourcePreset());
    }

    public function test__setAttempt()
    {
        $preset = $this->createStub(AuthSource::class);
        $source = new Source($preset);

        $this->assertInstanceOf(Attempt::class, $source->getAttempt());

        $attempt = $this->createStub(Attempt::class);
        $this->assertSame($source, $source->setAttempt($attempt));
        $this->assertSame($attempt, $source->getAttempt());

        $this->assertSame($source, $source->setAttempt(null));
        $attempt = $source->getAttempt();
        $this->assertInstanceOf(Attempt::class, $attempt);
        $this->assertSame($attempt, $source->getAttempt());
    }

    public function test__attempt__inheritedAttributes()
    {
        $preset = $this->createStub(AuthSource::class);
        $source=  new Source($preset);

        $status = $this->createStub(Status::class);
        $connector = $this->createStub(Connector::class);
        $binder = $this->createStub(Binder::class);
        $source->setAuthStatus($status)
               ->setConnector($connector)
               ->setBinder($binder);

        $this->assertSame($status, $source->getAttempt()->getAuthStatus());
        $this->assertSame($connector, $source->getAttempt()->getConnector());
        $this->assertSame($binder, $source->getAttempt()->getBinder());

        $status = $this->createStub(Status::class);
        $connector = $this->createStub(Connector::class);
        $binder = $this->createStub(Binder::class);
        $source->setAuthStatus($status)
               ->setConnector($connector)
               ->setBinder($binder);

        // the (old) attempt still keeps old tools
        $this->assertNotSame($status, $source->getAttempt()->getAuthStatus());
        $this->assertNotSame($connector, $source->getAttempt()->getConnector());
        $this->assertNotSame($binder, $source->getAttempt()->getBinder());

        // this way attempt is recreated and current tools get bound to it
        $this->assertSame($source, $source->setAttempt(null));

        $this->assertSame($status, $source->getAttempt()->getAuthStatus());
        $this->assertSame($connector, $source->getAttempt()->getConnector());
        $this->assertSame($binder, $source->getAttempt()->getBinder());
    }

    public function test__search()
    {
        $arguments = ['foo' => 'FOO'];

        $session = $this->createStub(Session::class);
        $search = $this->createStub(Search::class);

        $ldap = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->once())
                  ->method('createLdapWithSession')
                  ->with($session, $arguments)
                  ->will($this->returnValue($ldap));

        $rawEntries = [ $this->getMockBuilder(EntryInterface::class)->getMock() ];

        $result = $this->createMock(ResultInterface::class);
        $result->expects($this->once())
               ->method('getEntries')
               ->with(false)
               ->will($this->returnValue($rawEntries));

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('search')
               ->with()
               ->will($this->returnValue($search));
        $preset->expects($this->once())
               ->method('sessions')
               ->with()
               ->will($this->returnValue([$session]));

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->once())
               ->method('search')
               ->with($search, $ldap, $arguments)
               ->will($this->returnValue($result));

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->search($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertInstanceOf(Entry::class, $entries[0]);
        $this->assertSame($rawEntries[0], $entries[0]->getEntry());
    }

    public function test__locate()
    {
        $arguments = ['foo' => 'FOO'];

        $session = $this->createStub(Session::class);
        $search = $this->createStub(Search::class);

        $ldap = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->once())
                  ->method('createLdapWithSession')
                  ->with($session, $arguments)
                  ->will($this->returnValue($ldap));

        $rawEntries = [ $this->getMockBuilder(EntryInterface::class)->getMock() ];

        $result = $this->createMock(ResultInterface::class);
        $result->expects($this->once())
               ->method('getEntries')
               ->with(false)
               ->will($this->returnValue($rawEntries));

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('locate')
               ->with()
               ->will($this->returnValue($search));
        $preset->expects($this->once())
               ->method('sessions')
               ->with()
               ->will($this->returnValue([$session]));

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->once())
               ->method('search')
               ->with($search, $ldap, $arguments)
               ->will($this->returnValue($result));

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->locate($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertInstanceOf(Entry::class, $entries[0]);
        $this->assertSame($rawEntries[0], $entries[0]->getEntry());
    }

    public function test__search__Failover__1()
    {
        $arguments = ['foo' => 'FOO'];

        $exception = new LdapException("Can't connect to LDAP", -1);

        $session1 = $this->createStub(Session::class);
        $session2 = $this->createStub(Session::class);
        $search = $this->createStub(Search::class);

        $ldap = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->exactly(2))
                  ->method('createLdapWithSession')
                  ->withConsecutive([$session1, $arguments], [$session2, $arguments])
                  ->will($this->onConsecutiveCalls($this->throwException($exception), $ldap));

        $rawEntries = [ $this->getMockBuilder(EntryInterface::class)->getMock() ];

        $result = $this->createMock(ResultInterface::class);
        $result->expects($this->once())
               ->method('getEntries')
               ->with(false)
               ->will($this->returnValue($rawEntries));

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('search')
               ->with()
               ->will($this->returnValue($search));
        $preset->expects($this->once())
               ->method('sessions')
               ->with()
               ->will($this->returnValue([$session1, $session2]));

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->once())
               ->method('search')
               ->with($search, $ldap, $arguments)
               ->will($this->returnValue($result));

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->search($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertInstanceOf(Entry::class, $entries[0]);
        $this->assertSame($rawEntries[0], $entries[0]->getEntry());
    }

    public function test__search__Failover__2()
    {
        $arguments = ['foo' => 'FOO'];

        $exception = new LdapException("Can't connect to LDAP", -1);

        $session1 = $this->createStub(Session::class);
        $session2 = $this->createStub(Session::class);
        $search = $this->createStub(Search::class);

        $ldap1 = $this->getMockBuilder(LdapInterface::class)->getMock();
        $ldap2 = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->exactly(2))
                  ->method('createLdapWithSession')
                  ->withConsecutive([$session1, $arguments], [$session2, $arguments])
                  ->will($this->onConsecutiveCalls($ldap1, $ldap2));

        $rawEntries = [ $this->getMockBuilder(EntryInterface::class)->getMock() ];

        $result = $this->createMock(ResultInterface::class);
        $result->expects($this->once())
               ->method('getEntries')
               ->with(false)
               ->will($this->returnValue($rawEntries));

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('search')
               ->with()
               ->will($this->returnValue($search));
        $preset->expects($this->once())
               ->method('sessions')
               ->with()
               ->will($this->returnValue([$session1, $session2]));

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->exactly(2))
               ->method('search')
               ->withConsecutive(
                   [$search, $ldap1, $arguments],
                   [$search, $ldap2, $arguments]
               )
               ->will($this->onConsecutiveCalls(
                   $this->throwException($exception),
                   $this->returnValue($result)
               ));

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->search($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertInstanceOf(Entry::class, $entries[0]);
        $this->assertSame($rawEntries[0], $entries[0]->getEntry());
    }

    public function test__search__Failover__3()
    {
        $arguments = ['foo' => 'FOO'];

        $exception = new LdapException("Can't connect to LDAP", -1);

        $session1 = $this->createStub(Session::class);
        $session2 = $this->createStub(Session::class);
        $search = $this->createStub(Search::class);

        $ldap1 = $this->getMockBuilder(LdapInterface::class)->getMock();
        $ldap2 = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->exactly(2))
                  ->method('createLdapWithSession')
                  ->withConsecutive([$session1, $arguments], [$session2, $arguments])
                  ->will($this->onConsecutiveCalls($ldap1, $ldap2));

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('search')
               ->with()
               ->will($this->returnValue($search));
        $preset->expects($this->once())
               ->method('sessions')
               ->with()
               ->will($this->returnValue([$session1, $session2]));

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->exactly(2))
               ->method('search')
               ->withConsecutive(
                   [$search, $ldap1, $arguments],
                   [$search, $ldap2, $arguments]
               )
               ->will($this->onConsecutiveCalls(
                   $this->throwException($exception),
                   $this->throwException($exception)
               ));

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->search($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(0, $entries);
    }

    public function test__locate__Failover__1()
    {
        $arguments = ['foo' => 'FOO'];

        $exception = new LdapException("Can't connect to LDAP", -1);

        $session1 = $this->createStub(Session::class);
        $session2 = $this->createStub(Session::class);
        $search = $this->createStub(Search::class);

        $ldap = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->exactly(2))
                  ->method('createLdapWithSession')
                  ->withConsecutive([$session1, $arguments], [$session2, $arguments])
                  ->will($this->onConsecutiveCalls($this->throwException($exception), $ldap));

        $rawEntries = [ $this->getMockBuilder(EntryInterface::class)->getMock() ];

        $result = $this->createMock(ResultInterface::class);
        $result->expects($this->once())
               ->method('getEntries')
               ->with(false)
               ->will($this->returnValue($rawEntries));

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('locate')
               ->with()
               ->will($this->returnValue($search));
        $preset->expects($this->once())
               ->method('sessions')
               ->with()
               ->will($this->returnValue([$session1, $session2]));

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->once())
               ->method('search')
               ->with($search, $ldap, $arguments)
               ->will($this->returnValue($result));

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->locate($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertInstanceOf(Entry::class, $entries[0]);
        $this->assertSame($rawEntries[0], $entries[0]->getEntry());
    }

    public function test__locate__Failover__2()
    {
        $arguments = ['foo' => 'FOO'];

        $exception = new LdapException("Can't connect to LDAP", -1);

        $session1 = $this->createStub(Session::class);
        $session2 = $this->createStub(Session::class);
        $search = $this->createStub(Search::class);

        $ldap1 = $this->getMockBuilder(LdapInterface::class)->getMock();
        $ldap2 = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->exactly(2))
                  ->method('createLdapWithSession')
                  ->withConsecutive([$session1, $arguments], [$session2, $arguments])
                  ->will($this->onConsecutiveCalls($ldap1, $ldap2));

        $rawEntries = [ $this->getMockBuilder(EntryInterface::class)->getMock() ];

        $result = $this->createMock(ResultInterface::class);
        $result->expects($this->once())
               ->method('getEntries')
               ->with(false)
               ->will($this->returnValue($rawEntries));

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('locate')
               ->with()
               ->will($this->returnValue($search));
        $preset->expects($this->once())
               ->method('sessions')
               ->with()
               ->will($this->returnValue([$session1, $session2]));

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->exactly(2))
               ->method('search')
               ->withConsecutive(
                   [$search, $ldap1, $arguments],
                   [$search, $ldap2, $arguments]
               )
               ->will($this->onConsecutiveCalls(
                   $this->throwException($exception),
                   $this->returnValue($result)
               ));

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->locate($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertInstanceOf(Entry::class, $entries[0]);
        $this->assertSame($rawEntries[0], $entries[0]->getEntry());
    }

    public function test__locate__Failover__3()
    {
        $arguments = ['foo' => 'FOO'];

        $exception = new LdapException("Can't connect to LDAP", -1);

        $session1 = $this->createStub(Session::class);
        $session2 = $this->createStub(Session::class);
        $search = $this->createStub(Search::class);

        $ldap1 = $this->getMockBuilder(LdapInterface::class)->getMock();
        $ldap2 = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->exactly(2))
                  ->method('createLdapWithSession')
                  ->withConsecutive([$session1, $arguments], [$session2, $arguments])
                  ->will($this->onConsecutiveCalls($ldap1, $ldap2));

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('locate')
               ->with()
               ->will($this->returnValue($search));
        $preset->expects($this->once())
               ->method('sessions')
               ->with()
               ->will($this->returnValue([$session1, $session2]));

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->exactly(2))
               ->method('search')
               ->withConsecutive(
                   [$search, $ldap1, $arguments],
                   [$search, $ldap2, $arguments]
               )
               ->will($this->onConsecutiveCalls(
                   $this->throwException($exception),
                   $this->throwException($exception)
               ));

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->locate($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(0, $entries);
    }

    public function test__search__nullSearchPreset()
    {
        $arguments = ['foo' => 'FOO'];

        $ldap = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->never())
                  ->method('createLdapWithSession');

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('search')
               ->with()
               ->will($this->returnValue(null));
        $preset->expects($this->never())
               ->method('sessions');

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->never())
               ->method('search');

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->search($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(0, $entries);
    }

    public function test__locate__nullSearchPreset()
    {
        $arguments = ['foo' => 'FOO'];

        $ldap = $this->getMockBuilder(LdapInterface::class)->getMock();

        $connector = $this->createMock(Connector::class);
        $connector->expects($this->never())
                  ->method('createLdapWithSession');

        $preset = $this->createStub(AuthSource::class);
        $preset->expects($this->once())
               ->method('locate')
               ->with()
               ->will($this->returnValue(null));
        $preset->expects($this->never())
               ->method('sessions');

        $finder = $this->createMock(Finder::class);
        $finder->expects($this->never())
               ->method('search');

        $source = new Source($preset);
        $source->setConnector($connector)
               ->setFinder($finder);

        $entries = $source->locate($arguments);
        $this->assertIsArray($entries);
        $this->assertCount(0, $entries);
    }
}
