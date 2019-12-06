<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\RelatedPreset;
use Cruftman\Support\Preset;
use Cruftman\Support\PresetInterface;
use Cruftman\Support\PresetsAggregateInterface;
use Cruftman\Support\Exceptions\OptionNotFoundException;

class RelatedPresetTest extends TestCase
{
    public function test__getRelatedPreset()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();
        $options = ['foo' => 'foo1', 'bar' => 'bar1'];
        $preset = new class ($options, $aggregate) extends Preset {
            use RelatedPreset { getRelatedPreset as public; }
        };

        $other = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        $aggregate->expects($this->exactly(3))
                  ->method('getNamedPreset')
                  ->withConsecutive(['Foo', 'foo1'], ['Foo', 'foo1'], ['Bar', 'bar1'])
                  ->will($this->onConsecutiveCalls($other, $other, null));

        $this->assertSame($other, $preset->getRelatedPreset('Foo', 'foo')); // ok
        $this->assertSame($other, $preset->getRelatedPreset('Foo', 'foo', $default)); // ok
        $this->assertNull($preset->getRelatedPreset('Bar', 'bar'));         // bar1 not in array of Bar presets
        $this->assertNull($preset->getRelatedPreset('Geez', 'geez'));       // geez option is not defined
        $this->assertSame($default, $preset->getRelatedPreset('Geez','geez', $default)); // default value
    }

    public function test__getRelatedPresetOrFail()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();
        $options = ['foo' => 'foo1', 'bar' => 'bar1'];
        $preset = new class ($options, $aggregate) extends Preset {
            use RelatedPreset { getRelatedPresetOrFail as public; }
        };

        $other = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        $aggregate->expects($this->once())
                  ->method('getNamedPreset')
                  ->with('Foo', 'foo1')
                  ->willReturn($other);

        $this->assertSame($other, $preset->getRelatedPresetOrFail('Foo', 'foo')); // ok
    }

    public function test__getRelatedPresetOrFail__throwsPresetException()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();

        $options = ['foo' => 'foo1', 'bar' => 'bar1'];
        $preset = new class ($options, $aggregate) extends Preset {
            use RelatedPreset { getRelatedPresetOrFail as public; }
        };

        $other = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        // $exception imitates our PresetException
        $exception = new class ("troubles here") extends \Exception {};
        $aggregate->expects($this->exactly(1))
                  ->method('getNamedPreset')
                  ->with('Bar', 'bar1')
                  ->willThrowException($exception);

        $this->expectException(get_class($exception));
        $this->expectExceptionMessage("troubles here");
        $preset->getRelatedPresetOrFail('Bar', 'bar');         // bar1 not in array of Bar presets
    }

    public function test__getRelatedPresetOrFail__throwsOptionNotFound()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();

        $options = ['foo' => 'foo1', 'bar' => 'bar1'];
        $preset = new class ($options, $aggregate) extends Preset {
            use RelatedPreset { getRelatedPresetOrFail as public; }
        };

        $other = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        $aggregate->expects($this->never())
                  ->method('getNamedPreset');

        $this->expectException(OptionNotFoundException::class);
        $this->expectExceptionMessage('option "geez" not found');
        $preset->getRelatedPresetOrFail('Geez', 'geez');         // bar1 not in array of Bar presets
    }
}
