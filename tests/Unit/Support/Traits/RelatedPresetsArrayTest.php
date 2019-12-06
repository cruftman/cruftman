<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\RelatedPresetsArray;
use Cruftman\Support\Preset;
use Cruftman\Support\PresetInterface;
use Cruftman\Support\PresetsAggregateInterface;
use Cruftman\Support\Exceptions\OptionNotFoundException;

class RelatedPresetsArrayTest extends TestCase
{
    public function test__getRelatedPresetsArray()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();

        $options = ['foo' => ['foo1', 'foo2'], 'bar' => ['bar1']];
        $preset = new class ($options, $aggregate) extends Preset {
            use RelatedPresetsArray { getRelatedPresetsArray as public; }
        };

        $foo1 = $this->createStub(PresetInterface::class);
        $foo2 = $this->createStub(PresetInterface::class);
        $bar1 = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        $aggregate->expects($this->exactly(3))
                  ->method('getNamedPreset')
                  ->withConsecutive(['Foo', 'foo1'], ['Foo', 'foo2'], ['Bar', 'bar1'])
                  ->will($this->onConsecutiveCalls($foo1, $foo2, $bar1));

        $this->assertSame([$foo1, $foo2], $preset->getRelatedPresetsArray('Foo', 'foo')); // ok
        $this->assertSame([$bar1], $preset->getRelatedPresetsArray('Bar', 'bar'));  // bar1 not in array of Bar presets
        $this->assertNull($preset->getRelatedPresetsArray('Geez', 'geez'));         // geez option is not defined
        $this->assertSame([$default], $preset->getRelatedPresetsArray('Geez','geez', [$default])); // default value
    }

    public function test__getRelatedPresetsArrayOrFail()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();

        $options = ['foo' => ['foo1', 'foo2'], 'bar' => ['bar1']];
        $preset = new class ($options, $aggregate) extends Preset {
            use RelatedPresetsArray { getRelatedPresetsArrayOrFail as public; }
        };

        $foo1 = $this->createStub(PresetInterface::class);
        $foo2 = $this->createStub(PresetInterface::class);
        $bar1 = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        $aggregate->expects($this->exactly(3))
                  ->method('getNamedPreset')
                  ->withConsecutive(['Foo', 'foo1'], ['Foo', 'foo2'], ['Bar', 'bar1'])
                  ->will($this->onConsecutiveCalls($foo1, $foo2, $bar1));

        $this->assertSame([$foo1, $foo2], $preset->getRelatedPresetsArrayOrFail('Foo', 'foo')); // ok
        $this->assertSame([$bar1], $preset->getRelatedPresetsArrayOrFail('Bar', 'bar'));  // bar1 not in array of Bar presets
    }

    public function test__getRelatedPresetsArrayOrFail__throwsPresetException()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();

        $options = ['foo' => ['foo1', 'foo2'], 'bar' => ['bar1']];
        $preset = new class ($options, $aggregate) extends Preset {
            use RelatedPresetsArray { getRelatedPresetsArrayOrFail as public; }
        };

        $foo1 = $this->createStub(PresetInterface::class);
        $foo2 = $this->createStub(PresetInterface::class);
        $bar1 = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        $exception = new class ("having trouble") extends \Exception {};
        $aggregate->expects($this->once())
                  ->method('getNamedPreset')
                  ->withConsecutive(['Foo', 'foo1'])
                  ->willThrowException($exception);

        $this->expectException(get_class($exception));
        $this->expectExceptionMessage("having trouble");
        $preset->getRelatedPresetsArrayOrFail('Foo', 'foo'); // ok
    }

    public function test__getRelatedPresetsArrayOrFail__throwsOptionNotFoundException()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();

        $options = ['foo' => ['foo1', 'foo2'], 'bar' => ['bar1']];
        $preset = new class ($options, $aggregate) extends Preset {
            use RelatedPresetsArray { getRelatedPresetsArrayOrFail as public; }
        };

        $foo1 = $this->createStub(PresetInterface::class);
        $foo2 = $this->createStub(PresetInterface::class);
        $bar1 = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        $aggregate->expects($this->never())
                  ->method('getNamedPreset');

        $this->expectException(OptionNotFoundException::class);
        $this->expectExceptionMessage('option "geez" not found');
        $preset->getRelatedPresetsArrayOrFail('Geez', 'geez');
    }
}
