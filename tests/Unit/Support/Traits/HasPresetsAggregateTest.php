<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\HasPresetsAggregate;
use Cruftman\Support\Preset\AggregateInterface;

class HasPresetsAggregateTest extends TestCase
{
    public function test__accessors()
    {
        $object = new class {
            use HasPresetsAggregate;
        };

        $this->assertNull($object->getPresetsAggregate());

        $stub = $this->createStub(AggregateInterface::class);
        $this->assertSame($object, $object->setPresetsAggregate($stub));

        $this->assertSame($stub, $object->getPresetsAggregate());
    }
}
