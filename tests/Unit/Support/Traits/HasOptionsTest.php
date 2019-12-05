<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\HasOptions;
use Cruftman\Support\Exceptions\OptionNotFoundException;

class HasOptionsTest extends TestCase
{
    public function test__methods()
    {
        $object = new class {
            use HasOptions;
        };
        $options = ['foo' => 'FOO', 'bar' => ['geez' => 'GEEZ']];

        $this->assertNull($object->getOptions());
        $this->assertNull($object->getOption('foo'));

        $this->assertSame($object, $object->setOptions($options));
        $this->assertSame($options, $object->getOptions());
        $this->assertSame('FOO', $object->getOption('foo'));
        $this->assertSame(['geez' => 'GEEZ'], $object->getOption('bar'));
        $this->assertSame('GEEZ', $object->getOption('bar.geez'));
    }

    public function test__setOptions__withValidation()
    {
        $object = new class {
            use HasOptions;

            public function validateOptions(array $options)
            {
                return array_merge($options, ['validated' => true]);
            }
        };

        $this->assertSame($object, $object->setOptions(['foo' => 'FOO']));
        $this->assertSame(['foo' => 'FOO', 'validated' => true], $object->getOptions());
    }

    public function test__setOptions__withWrapper()
    {
        $object = new class {
            use HasOptions;

            public function wrapOptions(array $options)
            {
                return [ $options ];
            }
        };

        $this->assertSame($object, $object->setOptions(['foo' => 'FOO']));
        $this->assertSame([['foo' => 'FOO']], $object->getOptions());
    }

    public function test__setOptionsPrefix()
    {
        $object = new class {
            use HasOptions;
        };

        $this->assertNull($object->getOptionsPrefix('foo.bar'));
        $this->assertSame($object, $object->setOptionsPrefix('foo.bar'));
        $this->assertSame('foo.bar', $object->getOptionsPrefix());
    }

    public function test__getPrefixedOptionsKey()
    {
        $object = new class {
            use HasOptions;
        };
        $this->assertSame('geez', $object->getPrefixedOptionKey('geez'));
        $this->assertSame($object, $object->setOptionsPrefix('foo.bar'));
        $this->assertSame('foo.bar.geez', $object->getPrefixedOptionKey('geez'));
    }

    public function test__getOptionOrFail()
    {
        $object = new class {
            use HasOptions;
        };

        $object->setOptions(['foo' => 'FOO', 'bar' => ['geez' => 'GEEZ'], 'null' => null]);

        $this->assertSame('FOO', $object->getOptionOrFail('foo'));
        $this->assertSame(['geez' => 'GEEZ'], $object->getOptionOrFail('bar'));
        $this->assertSame('GEEZ', $object->getOptionOrFail('bar.geez'));
        $this->assertNull($object->getOptionOrFail('null'));
    }

    public function test__getOptionOrFail__throwsOptionNotFoundException()
    {
        $object = new class {
            use HasOptions;
        };

        $object->setOptions(['foo' => 'FOO']);

        $this->expectException(OptionNotFoundException::class);
        $this->expectExceptionMessage('option "bar" not found');

        $object->getOptionOrFail('bar');
    }

    public function test__getOptionOrFail__withOptionsPrefix__throwsOptionNotFoundException()
    {
        $object = new class {
            use HasOptions;
        };

        $object->setOptions(['geez' => 'GEEZ']);
        $object->setOptionsPrefix("foo");

        $this->expectException(OptionNotFoundException::class);
        $this->expectExceptionMessage('option "foo.bar" not found');

        $object->getOptionOrFail('bar');
    }
}
