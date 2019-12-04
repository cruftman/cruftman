<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\RelatedPreset;
use Cruftman\Support\Preset;
use Cruftman\Support\PresetInterface;
use Cruftman\Support\PresetsAggregateInterface;

class RelatedPresetTest extends TestCase
{
    public function test__getRelatedPreset()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();

        $preset = new class ($aggregate, ['foo' => 'foo1', 'bar' => 'bar1']) extends Preset {
            use RelatedPreset;
            public function tGetRelatedPreset(string $class, string $key, ...$args) {
                return $this->getRelatedPreset($class, $key, ...$args);
            }
        };

        $other = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        $aggregate->expects($this->exactly(3))
                  ->method('getNamedPreset')
                  ->withConsecutive(['Foo', 'foo1'], ['Foo', 'foo1'], ['Bar', 'bar1'])
                  ->will($this->onConsecutiveCalls($other, $other, null));

        $this->assertSame($other, $preset->tGetRelatedPreset('Foo', 'foo')); // ok
        $this->assertSame($other, $preset->tGetRelatedPreset('Foo', 'foo', $default)); // ok
        $this->assertNull($preset->tGetRelatedPreset('Bar', 'bar'));         // bar1 not in array of Bar presets
        $this->assertNull($preset->tGetRelatedPreset('Geez', 'geez'));       // geez option is not defined
        $this->assertSame($default, $preset->tGetRelatedPreset('Geez','geez', $default)); // default value
    }

    public function test__getRelatedPresetOrFail()
    {
        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
                          ->getMock();

        $preset = new class ($aggregate, ['foo' => 'foo1', 'bar' => 'bar1']) extends Preset {
            use RelatedPreset;
            public function tGetRelatedPreset(string $class, string $key, ...$args) {
                return $this->getRelatedPreset($class, $key, ...$args);
            }
        };

        $other = $this->createStub(PresetInterface::class);
        $default = $this->createStub(PresetInterface::class);

        $aggregate->expects($this->exactly(3))
                  ->method('getNamedPreset')
                  ->withConsecutive(['Foo', 'foo1'], ['Foo', 'foo1'], ['Bar', 'bar1'])
                  ->will($this->onConsecutiveCalls($other, $other, null));

        $this->assertSame($other, $preset->tGetRelatedPreset('Foo', 'foo')); // ok
        $this->assertSame($other, $preset->tGetRelatedPreset('Foo', 'foo', $default)); // ok
        $this->assertNull($preset->tGetRelatedPreset('Bar', 'bar'));         // bar1 not in array of Bar presets
        $this->assertNull($preset->tGetRelatedPreset('Geez', 'geez'));       // geez option is not defined
        $this->assertSame($default, $preset->tGetRelatedPreset('Geez','geez', $default)); // default value
    }

//    public function test__getRelatedPresetOrFail()
//    {
//        $aggregate = $this->getMockBuilder(PresetsAggregateInterface::class)
//                          ->getMock();
//
//        $preset = new class ($aggregate, ['foo' => 'foo1', 'bar' => 'bar1']) extends Preset {
//            public function tGetRelatedPresetOrFail(string $class, string $key, ...$args) {
//                return $this->getRelatedPresetOrFail($class, $key, ...$args);
//            }
//        };
//
//        $other = $this->createStub(PresetInterface::class);
//
//        $aggregate->expects($this->exactly(2))
//                  ->method('getNamedPreset')
//                  ->withConsecutive(['Foo', 'foo1'], ['Bar', 'bar1'])
//                  ->will($this->onConsecutiveCalls($other, $other));
//
//        $this->assertSame($other, $preset->tGetRelatedPresetOrFail('Foo', 'foo')); // ok
//        $this->assertSame($other, $preset->tGetRelatedPresetOrFail('Bar', 'bar')); // ok
//        //$this->assertNull($preset->tGetRelatedPresetOrFail('Bar', 'bar'));         // bar1 not in array of Bar presets
//        //$this->assertNull($preset->tGetRelatedPresetOrFail('Geez', 'geez'));       // geez option is not defined
//    }
}
