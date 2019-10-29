<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\HasOptions;

class HasOptionsTest extends TestCase
{
    public function test__HasOptions__methods()
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

    public function test__HasOptions__withValidation()
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

    public function test__HasOptions__withWrapper()
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
}
