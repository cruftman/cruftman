<?php

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Preset;
use Cruftman\Support\PresetInterface;
use Cruftman\Support\TemplateArray;
use Cruftman\Support\PresetsAggregateInterface;
use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Support\Traits\HasPresetsAggregate;

class PresetTest extends TestCase
{
    public function test__use__HasTemplateOptions()
    {
        $traits = class_uses(Preset::class);
        $this->assertContains(HasTemplateOptions::class, $traits);
    }

    public function test__use__HasPresetsAggregate()
    {
        $traits = class_uses(Preset::class);
        $this->assertContains(HasPresetsAggregate::class, $traits);
    }

    public function test__construct__withOptions()
    {
        $options = ['foo' => 'FOO'];
        $preset = new Preset($options);

        $this->assertNull($preset->getPresetsAggregate());
        $this->assertInstanceOf(TemplateArray::class, $preset->getOptions());
        $this->assertSame($options, $preset->getOptions()->getArrayCopy());
    }

    public function test__construct__withOptionsAndAggregate()
    {
        $aggregate = $this->createStub(PresetsAggregateInterface::class);
        $options = ['foo' => 'FOO'];

        $preset = new Preset($options, $aggregate);

        $this->assertSame($aggregate, $preset->getPresetsAggregate());
        $this->assertInstanceOf(TemplateArray::class, $preset->getOptions());
        $this->assertSame($options, $preset->getOptions()->getArrayCopy());
    }
}
