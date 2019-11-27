<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\DecoratesEntryInterface;
use Korowai\Lib\Ldap\EntryInterface;

class DecoratesEntryInterfaceTest extends TestCase
{
    protected function decorateEntryInterface(?EntryInterface $entryInterface = null)
    {
        return new class ($entryInterface) implements EntryInterface {
            use DecoratesEntryInterface;
            protected $entryInterface = null;
            public function __construct(?EntryInterface $entryInterface = null) {
                $this->entryInterface = $entryInterface;
            }
            public function getEntry() : ?EntryInterface
            {
                return $this->entryInterface;
            }
        };
    }

    public function test__getDn()
    {
        $mock = $this->getMockBuilder(EntryInterface::class)
                     ->getMock();
        $mock->expects($this->once())
             ->method('getDn')
             ->with()
             ->willReturn('dc=example,dc=org');

        $entry = $this->decorateEntryInterface($mock);

        $this->assertSame('dc=example,dc=org', $entry->getDn());
    }

    public function test__setDn()
    {
        $mock = $this->getMockBuilder(EntryInterface::class)
                     ->getMock();
        $mock->expects($this->once())
             ->method('setDn')
             ->with('dc=example,dc=org')
             ->willReturn('ok');

        $entry = $this->decorateEntryInterface($mock);

        $this->assertSame('ok', $entry->setDn('dc=example,dc=org'));
    }

    public function test__getAttributes()
    {
        $mock = $this->getMockBuilder(EntryInterface::class)
                     ->getMock();
        $mock->expects($this->once())
             ->method('getAttributes')
             ->with()
             ->willReturn(['a', 'b']);

        $entry = $this->decorateEntryInterface($mock);

        $this->assertSame(['a', 'b'], $entry->getAttributes());
    }

    public function test__setAttributes()
    {
        $mock = $this->getMockBuilder(EntryInterface::class)
                     ->getMock();
        $mock->expects($this->once())
             ->method('setAttributes')
             ->with(['a', 'b'])
             ->willReturn('ok');

        $entry = $this->decorateEntryInterface($mock);

        $this->assertSame('ok', $entry->setAttributes(['a', 'b']));
    }

    public function test__getAttribute()
    {
        $mock = $this->getMockBuilder(EntryInterface::class)
                     ->getMock();
        $mock->expects($this->once())
             ->method('getAttribute')
             ->with('foo')
             ->willReturn(['a', 'b']);

        $entry = $this->decorateEntryInterface($mock);

        $this->assertSame(['a', 'b'], $entry->getAttribute('foo'));
    }

    public function test__setAttribute()
    {
        $mock = $this->getMockBuilder(EntryInterface::class)
                     ->getMock();
        $mock->expects($this->once())
             ->method('setAttribute')
             ->with('foo', ['a', 'b'])
             ->willReturn('ok');

        $entry = $this->decorateEntryInterface($mock);

        $this->assertSame('ok', $entry->setAttribute('foo', ['a', 'b']));
    }

    public function test__hasAttribute()
    {
        $mock = $this->getMockBuilder(EntryInterface::class)
                     ->getMock();
        $mock->expects($this->once())
             ->method('hasAttribute')
             ->with('foo')
             ->willReturn(true);

        $entry = $this->decorateEntryInterface($mock);

        $this->assertTrue($entry->hasAttribute('foo'));
    }
}
