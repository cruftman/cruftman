<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\HasPresetsAggregate;
use Cruftman\Support\PresetsAggregateInterface;

class HasPresetsAggregateTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasPresetsAggregate;
        };

        $this->assertNull($object->getPresetsAggregate());

        $stub = $this->createStub(PresetsAggregateInterface::class);
        $this->assertSame($object, $object->setPresetsAggregate($stub));

        $this->assertSame($stub, $object->getPresetsAggregate());

        $this->assertSame($object, $object->setPresetsAggregate(null));
        $this->assertNull($object->getPresetsAggregate());
    }
}
