<?php

namespace Tests\Unit\Ldap\Auth;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Auth\Entry;
use Cruftman\Ldap\Auth\Source;
use Korowai\Lib\Ldap\EntryInterface;
use Cruftman\Ldap\Traits\HasConnectionPreset;
use Cruftman\Ldap\Traits\HasEntry;
use Cruftman\Ldap\Traits\DecoratesEntryInterface;

class EntryTest extends TestCase
{
    public function test__implements__EntryInterface()
    {
        $interfaces = class_implements(Entry::class);
        $this->assertContains(EntryInterface::class, $interfaces);
    }

    public function test__uses__HasConnectionPreset()
    {
        $uses = class_uses(Entry::class);
        $this->assertContains(HasConnectionPreset::class, $uses);
    }

    public function test__uses__HasEntry()
    {
        $uses = class_uses(Entry::class);
        $this->assertContains(HasEntry::class, $uses);
    }

    public function test__uses__DecoratesEntryInterface()
    {
        $uses = class_uses(Entry::class);
        $this->assertContains(DecoratesEntryInterface::class, $uses);
    }

    public function test__construct()
    {
        $mock = $this->getMockBuilder(EntryInterface::class)->getMock();
        $entry = new Entry($mock);
        $this->assertSame($mock, $entry->getEntry());
    }

    public function test__setSource()
    {
        $mock = $this->getMockBuilder(EntryInterface::class)->getMock();
        $source = $this->createStub(Source::class);
        $entry = new Entry($mock);

        $this->assertNull($entry->getSource());
        $this->assertSame($entry, $entry->setSource($source));
        $this->assertSame($source, $entry->getSource());
        $this->assertSame($entry, $entry->setSource(null));
        $this->assertNull($entry->getSource());
    }
}
