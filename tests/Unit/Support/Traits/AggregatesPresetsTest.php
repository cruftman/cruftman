<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\AggregatesPresets;
use Cruftman\Support\PresetInterface;
use Cruftman\Support\PresetsAggregateInterface;
use Cruftman\Support\Exceptions\PresetException;
use Cruftman\Support\Exceptions\OptionNotFoundException;
use Illuminate\Support\Arr;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class AggregatesPresetsTest extends TestCase
{
    protected function allMethods() : array
    {
        return [
            'isValidOptionKey',
            'allKeysAreValidOptionKeys',
            'configurePresetOptionsResolver',
            'getPresetClasses',
            'getPresetOptionsKey',
            'getPresetOptionsKeyOrFail',
            'getNamedPresetOptions',
            'getNamedPresetOptionsOrFail',
            'getSingletonPresetOptions',
            'getSingletonPresetOptionsOrFail',
            'getNamedPresetsNames',
            'setNamedPreset',
            'getNamedPreset',
            'getNamedPresetByName',
            'createNamedPreset',
            'getSingletonPreset',
            'createSingletonPreset',
        ];
    }

    protected function allMethodsExcept(array $except) : array
    {
        return array_filter($this->allMethods(), function ($m) use ($except) {
            return !in_array($m, $except);
        });
    }

    protected function preventCallingTraitMethdosExcept($mock, array $except)
    {
        // expect no methods other than these two
        foreach($this->allMethodsExcept($except) as $method) {
            $mock->expects($this->never())->method($method);
        }
    }

    protected function createTestObject(?array $options = null)
    {
        return new class ($options) {
            use AggregatesPresets {
                // public - because we test these methods directly
                configurePresetOptionsResolver as public;
                getPresetOptionsKey as public;
                getPresetOptionsKeyOrFail as public;
                getNamedPresetOptions as public;
                getNamedPresetOptionsOrFail as public;
                getSingletonPresetOptions as public;
                getSingletonPresetOptionsOrFail as public;
            }
            public function __construct(?array $options = null) {
                $this->options = $options;
            }

            protected function getOption(string $name, $default = null) {
                return Arr::get($this->options, $name, $default);
            }

            protected function getOptionOrFail(string $name) {
                $notfound = new class{};
                $option = $this->getOption($name, $notfound);
                if ($option === $notfound) {
                    throw new \InvalidArgumentException('no such option');
                }
                return $option;
            }

            protected function getPresetKeysByClasses() : array {
                return [
                    'Klass\\Foo' => 'foos',
                    'Klass\\Bar' => 'bars',
                    'Klass\\Geez' => 'geez'
                ];
            }

            protected function isSingletonPreset(string $class) : ?bool {
                return array_key_exists($class, $this->getPresetKeysByClasses()) ? $class === 'Klass\\Geez' : null;
            }

            protected function createPresetWithOptions(string $class, array $options) : PresetInterface {
                return $class($this, $options);
            }
        };
    }

    protected function createTestObject2(?array $options = null)
    {
        return new class ($options) implements PresetsAggregateInterface {
            use AggregatesPresets;
            public function __construct(?array $options = null) {
                $this->options = $options;
            }

            protected function getOption(string $name, $default = null) {
                return Arr::get($this->options, $name, $default);
            }

            protected function getOptionOrFail(string $name) {
                $notfound = new class{};
                $option = $this->getOption($name, $notfound);
                if ($option === $notfound) {
                    throw new \InvalidArgumentException('no such option');
                }
                return $option;
            }

            protected function getPresetKeysByClasses() : array {
                return [
                    'Klass\\Foo' => 'foos',
                    'Klass\\Bar' => 'bars',
                    'Klass\\Geez' => 'geez'
                ];
            }

            public function isSingletonPreset(string $class) : ?bool {
                return array_key_exists($class, $this->getPresetKeysByClasses()) ? $class === 'Klass\\Geez' : null;
            }

            protected function createPresetWithOptions(string $class, array $options) : PresetInterface {
                return $class($this, $options);
            }
        };
    }

    public function test__configurePresetOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $aggreg = $this->createTestObject();
        $this->assertNull($aggreg->configurePresetOptionsResolver($resolver));

        $this->assertSame([], $resolver->resolve([]));

        $options = [
            'foos' => [ // named preset
                'foo1' => [
                    'foo1opt1' => 'FOO1OPT1',
                    'foo1opt2' => 'FOO1OPT2'
                ]
            ],
            'bars' => [
                'bar1' => [
                    'bar1opt1' => 'BAR1OPT1',
                    'bar1opt2' => 'BAR1OPT2'
                ],
                'bar2' => [
                ]
            ],
            'geez' => [ // singleton preset
                'opt1' => 'OPT1',
                'opt2' => 'OPT2'
            ],
        ];
        $this->assertSame($options, $resolver->resolve($options));
    }

    public function test__configurePresetOptionsResolver__invalidOptionKey()
    {
        $resolver = new OptionsResolver();
        $aggreg = $this->createTestObject();
        $this->assertNull($aggreg->configurePresetOptionsResolver($resolver));

        $options = [
            'foos' => [ // named preset
                'dots.are.forbidden' => [
                ]
            ],
        ];
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('option "foos" with value array');
        $this->assertSame($options, $resolver->resolve($options));
    }

    public function test__getPresetClasses()
    {
        $aggreg = $this->createTestObject();
        $this->assertSame(['Klass\\Foo', 'Klass\\Bar', 'Klass\Geez'], $aggreg->getPresetClasses());
    }

    public function test__getPresetOptionsKey()
    {
        $aggreg = $this->createTestObject();
        $this->assertSame('foos', $aggreg->getPresetOptionsKey('Klass\\Foo'));
        $this->assertSame('bars', $aggreg->getPresetOptionsKey('Klass\\Bar'));
        $this->assertNull($aggreg->getPresetOptionsKey('Fake\\Geez'));
    }

    public function test__getPresetOptionsKeyOrFail()
    {
        $aggreg = $this->createTestObject();
        $this->assertSame('foos', $aggreg->getPresetOptionsKeyOrFail('Klass\\Foo'));
        $this->assertSame('bars', $aggreg->getPresetOptionsKeyOrFail('Klass\\Bar'));

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Fake\\Geez" is not a supported preset class');
        $aggreg->getPresetOptionsKeyOrFail('Fake\\Geez');
    }

    public function test__setNamedPreset()
    {
        $aggreg = $this->createTestObject2(['foos' => []]);
        $preset = $this->getMockBuilder(PresetInterface::class)->getMock();

        $preset->expects($this->exactly(2))
               ->method('setPresetsAggregate')
               ->withConsecutive([$aggreg], [null])
               ->will($this->onConsecutiveCalls($preset, $preset));

        $this->assertSame($aggreg, $aggreg->setNamedPreset('Klass\\Foo', 'foo1', $preset));
        $this->assertSame($preset, $aggreg->getNamedPreset('Klass\\Foo', 'foo1'));

        $this->assertSame($aggreg, $aggreg->setNamedPreset('Klass\\Foo', 'foo1', null));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('no such option');
        $aggreg->getNamedPreset('Klass\\Foo', 'foo1');
    }

    public function test__getNamedPresetOptions()
    {
        $aggreg = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']], 'geez' => ['GEEZ']]);
        $this->assertSame(['FOO1'], $aggreg->getNamedPresetOptions('Klass\\Foo', 'foo1'));
        $this->assertSame(['BAR1'], $aggreg->getNamedPresetOptions('Klass\\Bar', 'bar1'));
        $this->assertNull($aggreg->getNamedPresetOptions('Klass\\Bar', 'bar2'));
        $this->assertNull($aggreg->getNamedPresetOptions('Fake\\Geez', 'geez1'));
        // With singleton it should return null as well.
        $this->assertNull($aggreg->getNamedPresetOptions('Klass\\Geez', 'geez1'));
    }

    public function test__getNamedPresetOptionsOrFail()
    {
        $aggreg = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);
        $this->assertSame(['FOO1'], $aggreg->getNamedPresetOptionsOrFail('Klass\\Foo', 'foo1'));
        $this->assertSame(['BAR1'], $aggreg->getNamedPresetOptionsOrFail('Klass\\Bar', 'bar1'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('no such option');
        $aggreg->getNamedPresetOptionsOrFail('Klass\\Bar', 'bar2');
    }

    public function test__getNamedPresetOptionsOrFail__isSingleton()
    {
        $aggreg = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Klass\\Geez" is a singleton preset');
        $aggreg->getNamedPresetOptionsOrFail('Klass\\Geez', 'geez1');
    }

    public function test__getNamedPresetOptionsOrFail__notAPresetClass()
    {
        $aggreg = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Fake\\Geez" is not a supported preset class');
        $aggreg->getNamedPresetOptionsOrFail('Fake\\Geez', 'geez2');
    }

    public function test__getSingletonPresetOptions()
    {
        $aggreg = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'geez' => ['GEEZ']]);
        $this->assertSame(['GEEZ'], $aggreg->getSingletonPresetOptions('Klass\\Geez'));
        $this->assertNull($aggreg->getSingletonPresetOptions('Fake\\Geez'));
        // it returns null for named preset
        $this->assertNull($aggreg->getSingletonPresetOptions('Klass\\Foo', 'foo1'));
    }

    public function test__getSingletonPresetOptionsOrFail()
    {
        $aggreg = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'geez' => ['GEEZ']]);
        $this->assertSame(['GEEZ'], $aggreg->getSingletonPresetOptionsOrFail('Klass\\Geez'));

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Fake\\Geez" is not a supported preset class');
        $aggreg->getSingletonPresetOptionsOrFail('Fake\\Geez');
    }

    public function test__getSingletonPresetOptionsOrFail__isNotSingleton()
    {
        $aggreg = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'geez' => ['GEEZ']]);

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Klass\\Foo" is not a singleton preset');
        $aggreg->getSingletonPresetOptionsOrFail('Klass\\Foo');
    }

    public function test__getNamedPresetsNames()
    {
        $aggreg = $this->createTestObject(['foos' => ['foo1' => ['FOO1'], 'foo2' => ['FOO2']],
                                           'bars' => ['bar1' => ['BAR1']]]);

        $this->assertSame(['foo1', 'foo2'], $aggreg->getNamedPresetsNames('Klass\\Foo'));
        $this->assertSame(['bar1'], $aggreg->getNamedPresetsNames('Klass\\Bar'));

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Fake\\Geez" is not a supported preset class');
        $aggreg->getNamedPresetsNames('Fake\\Geez');
    }

    public function test__getNamedPreset__withString()
    {
        $unsetMethods = [
            'getNamedPreset',
            'getNamedPresetByName',
            'createNamedPreset'
        ];
        $aggreg = $this->getMockBuilder(AggregatesPresets::class)
                       ->setMethods($this->allMethodsExcept($unsetMethods)) // setMethodsExcept() didn't work ...
                       ->getMockForTrait();

        // expect no methods other than these two
        $allowedMethods = array_merge($unsetMethods, [
                'getNamedPresetOptionsOrFail',
                'cretePresetWithOptions'
        ]);
        $this->preventCallingTraitMethdosExcept($aggreg, $allowedMethods);

        $aggreg->expects($this->once())
               ->method('getNamedPresetOptionsOrFail')
               ->with('Klass\\Foo', 'foo1')
               ->willReturn(['foo1opt1' => 'FOO1OPT1']);

        $preset = $this->createStub(PresetInterface::class);

        $aggreg->expects($this->once())
               ->method('createPresetWithOptions')
               ->with('Klass\\Foo', ['foo1opt1' => 'FOO1OPT1'])
               ->willReturn($preset);

        $this->assertSame($preset, $aggreg->getNamedPreset('Klass\\Foo', 'foo1'));
        $this->assertSame($preset, $aggreg->getNamedPreset('Klass\\Foo', 'foo1'));
    }

    public function test__getNamedPreset__withArray()
    {
        $unsetMethods = [
            'getNamedPreset',
            'createNamedPreset'
        ];
        $aggreg = $this->getMockBuilder(AggregatesPresets::class)
                       ->setMethods($this->allMethodsExcept($unsetMethods)) // setMethodsExcept() didn't work ...
                       ->getMockForTrait();

        // expect no methods other than these two
        $allowedMethods = array_merge($unsetMethods, [
                'cretePresetWithOptions'
        ]);
        $this->preventCallingTraitMethdosExcept($aggreg, $allowedMethods);

        $preset = $this->createStub(PresetInterface::class);

        $aggreg->expects($this->exactly(2))
               ->method('createPresetWithOptions')
               ->with('Klass\\Foo', ['foo1opt1' => 'FOO1OPT1'])
               ->willReturn($preset);

        $this->assertSame($preset, $aggreg->getNamedPreset('Klass\\Foo', ['foo1opt1' => 'FOO1OPT1']));
        $this->assertSame($preset, $aggreg->getNamedPreset('Klass\\Foo', ['foo1opt1' => 'FOO1OPT1']));
    }

    public function test__getSingletonPreset__withoutOptions()
    {
        $unsetMethods = [
            'getSingletonPreset',
            'createSingletonPreset'
        ];
        $aggreg = $this->getMockBuilder(AggregatesPresets::class)
                       ->setMethods($this->allMethodsExcept($unsetMethods)) // setMethodsExcept() didn't work ...
                       ->getMockForTrait();

        // expect no methods other than these two
        $allowedMethods = array_merge($unsetMethods, [
                'getSingletonPresetOptionsOrFail',
                'cretePresetWithOptions'
        ]);
        $this->preventCallingTraitMethdosExcept($aggreg, $allowedMethods);

        $aggreg->expects($this->once())
               ->method('getSingletonPresetOptionsOrFail')
               ->with('Klass\\Geez')
               ->willReturn(['geezopt1' => 'GEEZOPT1']);

        $preset = $this->createStub(PresetInterface::class);

        $aggreg->expects($this->once())
               ->method('createPresetWithOptions')
               ->with('Klass\\Geez', ['geezopt1' => 'GEEZOPT1'])
               ->willReturn($preset);

        $this->assertSame($preset, $aggreg->getSingletonPreset('Klass\\Geez'));
        $this->assertSame($preset, $aggreg->getSingletonPreset('Klass\\Geez'));
    }

    public function test__getSingletonPreset__withOptions()
    {
        $unsetMethods = [
            'getSingletonPreset',
            'createSingletonPreset'
        ];
        $aggreg = $this->getMockBuilder(AggregatesPresets::class)
                       ->setMethods($this->allMethodsExcept($unsetMethods)) // setMethodsExcept() didn't work ...
                       ->getMockForTrait();

        // expect no methods other than these two
        $allowedMethods = array_merge($unsetMethods, [
                'cretePresetWithOptions'
        ]);
        $this->preventCallingTraitMethdosExcept($aggreg, $allowedMethods);

        $preset = $this->createStub(PresetInterface::class);

        $aggreg->expects($this->exactly(2))
               ->method('createPresetWithOptions')
               ->with('Klass\\Geez', ['geezopt1' => 'FOO1OPT1'])
               ->willReturn($preset);

        $this->assertSame($preset, $aggreg->getSingletonPreset('Klass\\Geez', ['geezopt1' => 'FOO1OPT1']));
        $this->assertSame($preset, $aggreg->getSingletonPreset('Klass\\Geez', ['geezopt1' => 'FOO1OPT1']));
    }
}
