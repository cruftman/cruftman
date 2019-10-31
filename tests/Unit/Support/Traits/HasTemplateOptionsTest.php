<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\HasOptions;
use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Support\TemplateArray;
use Cruftman\Support\Exceptions\OptionNotFoundException;

class HasTemplateOptionsTest extends TestCase
{
    public function test__extends__HasOptions()
    {
        $uses = class_uses(HasTemplateOptions::class);
        $this->assertContains(HasOptions::class, $uses);
    }

    public function test__methods()
    {
        $object = new class {
            use HasTemplateOptions;
        };
        $options = ['foo' => 'this is ${foo}', 'bar' => ['geez' => 'this is ${geez}']];

        $this->assertNull($object->getOptions());
        $this->assertNull($object->getOption('foo'));

        // setOptions(), getOptions(), getOption()
        $this->assertSame($object, $object->setOptions($options));
        $this->assertInstanceOf(TemplateArray::class, $object->getOptions());
        $this->assertSame($options, $object->getOptions()->getArrayCopy());
        $this->assertSame('this is ${foo}', $object->getOption('foo'));
        $this->assertSame('this is ${geez}', $object->getOption('bar.geez'));

        // substOptions()
        $this->assertSame(
            ['foo' => 'this is FOO', 'bar' => ['geez' => 'this is GEEZ']],
            $object->substOptions(['foo' => 'FOO', 'geez' => 'GEEZ'])
        );

        // substOption()
        $this->assertSame('this is FOO', $object->substOption('foo', ['foo' => 'FOO']));
        $this->assertSame('this is GEEZ', $object->substOption('bar.geez', ['geez' => 'GEEZ']));
    }

    public function test__substOptionOrFail()
    {
        $object = new class {
            use HasTemplateOptions;
        };

        $object->setOptions(['foo' => '${foo}', 'bar' => ['geez' => '${geez}'], 'null' => null]);

        $this->assertSame('FOO', $object->substOptionOrFail('foo', ['foo' => 'FOO']));
        $this->assertSame(['geez' => 'GEEZ'], $object->substOptionOrFail('bar', ['geez' => 'GEEZ']));
        $this->assertSame('GEEZ', $object->substOptionOrFail('bar.geez', ['geez' => 'GEEZ']));
        $this->assertNull($object->substOptionOrFail('null'));
    }

    public function test__substOptionOrFail__throwsOptionNotFoundException()
    {
        $object = new class {
            use HasTemplateOptions;
        };

        $object->setOptions(['foo' => 'FOO']);

        $this->expectException(OptionNotFoundException::class);
        $this->expectExceptionMessage('option "bar" not found');

        $object->substOptionOrFail('bar');
    }
}
