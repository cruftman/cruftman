<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Support\Traits\HasOptions;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

class ValidatesOptionsTest extends TestCase
{
    public function test__ValidatesOptions()
    {
        $object = new class {
            use ValidatesOptions { validateOptions as public; }

            protected function configureOptionsResolver(OptionsResolver $resolver)
            {
                $resolver->setDefined(['foo'])
                         ->setAllowedValues('foo', ['FOO'])
                         ->setNormalizer('foo', function (Options $options, $value) {
                             return '-'.$value.'-';
                         });
            }
        };

        $this->assertSame(['foo' => '-FOO-'], $object->validateOptions(['foo' => 'FOO']));
    }

    public function test__ValidatesOptions__exception()
    {
        $object = new class {
            use ValidatesOptions { validateOptions as public; }

            protected function configureOptionsResolver(OptionsResolver $resolver)
            {
            }
        };

        $this->expectException(UndefinedOptionsException::class);
        $this->expectExceptionMessage('"foo" does not exist');

        $object->validateOptions(['foo' => 'FOO']);
    }

    public function test__ValidatesOptions__with__HasOptions()
    {
        $object = new class {
            use HasOptions, ValidatesOptions;

            protected function configureOptionsResolver(OptionsResolver $resolver)
            {
                $resolver->setDefined(['foo'])
                         ->setAllowedValues('foo', ['FOO'])
                         ->setNormalizer('foo', function (Options $options, $value) {
                             return '-'.$value.'-';
                         });
            }
        };

        $this->assertSame($object, $object->setOptions(['foo' => 'FOO']));
        $this->assertSame('-FOO-', $object->getOption('foo'));
    }
}
