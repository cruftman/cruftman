<?php

namespace Tests\Unit\Support\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Support\Traits\HasPresets;
use Cruftman\Support\Preset\PresetInterface;
use Cruftman\Support\Exceptions\PresetException;
use Cruftman\Support\Exceptions\OptionNotFoundException;
use Illuminate\Support\Arr;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class HasPresetsTest extends TestCase
{
    protected function allTraitMethods() : array
    {
        return [
            'isValidOptionKey',
            'allKeysAreValidOptionKeys',
            'configurePresetOptionsResolver',
            'getPresetOptionsKey',
            'getPresetOptionsKeyOrFail',
            'getNamedPresetOptions',
            'getNamedPresetOptionsOrFail',
            'getSingletonPresetOptions',
            'getSingletonPresetOptionsOrFail',
            'getNamedPresetsNames',
            'getNamedPreset',
            'getNamedPresetByName',
            'createNamedPreset',
            'getSingletonPreset',
            'createSingletonPreset',
        ];
    }

    protected function allTraitMethodsExcept(array $except) : array
    {
        return array_filter($this->allTraitMethods(), function ($m) use ($except) {
            return !in_array($m, $except);
        });
    }

    protected function preventCallingTraitMethdosExcept($mock, array $except)
    {
        // expect no methods other than these two
        foreach($this->allTraitMethodsExcept($except) as $method) {
            $mock->expects($this->never())->method($method);
        }
    }

    protected function createTestObject(?array $options = null)
    {
        return new class ($options) {
            use HasPresets;
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
                    'Class\\Foo' => 'foos',
                    'Class\\Bar' => 'bars',
                    'Class\\Geez' => 'geez'
                ];
            }

            protected function isSingletonPreset(string $class) : ?bool {
                return array_key_exists($class, $this->getPresetKeysByClasses()) ? $class === 'Class\\Geez' : null;
            }

            protected function createPresetWithOptions(string $class, array $options) : PresetInterface {
                return $class($this, $options);
            }
        };
    }

    public function test__configurePresetOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $object = $this->createTestObject();
        $this->assertNull($object->configurePresetOptionsResolver($resolver));

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
        $object = $this->createTestObject();
        $this->assertNull($object->configurePresetOptionsResolver($resolver));

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

    public function test__getPresetOptionsKey()
    {
        $object = $this->createTestObject();
        $this->assertSame('foos', $object->getPresetOptionsKey('Class\\Foo'));
        $this->assertSame('bars', $object->getPresetOptionsKey('Class\\Bar'));
        $this->assertNull($object->getPresetOptionsKey('Fake\\Geez'));
    }

    public function test__getPresetOptionsKeyOrFail()
    {
        $object = $this->createTestObject();
        $this->assertSame('foos', $object->getPresetOptionsKeyOrFail('Class\\Foo'));
        $this->assertSame('bars', $object->getPresetOptionsKeyOrFail('Class\\Bar'));

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Fake\\Geez" is not a supported ldap preset class');
        $object->getPresetOptionsKeyOrFail('Fake\\Geez');
    }

    public function test__getNamedPresetOptions()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']], 'geez' => ['GEEZ']]);
        $this->assertSame(['FOO1'], $object->getNamedPresetOptions('Class\\Foo', 'foo1'));
        $this->assertSame(['BAR1'], $object->getNamedPresetOptions('Class\\Bar', 'bar1'));
        $this->assertNull($object->getNamedPresetOptions('Class\\Bar', 'bar2'));
        $this->assertNull($object->getNamedPresetOptions('Fake\\Geez', 'geez1'));
        // With singleton it should return null as well.
        $this->assertNull($object->getNamedPresetOptions('Class\\Geez', 'geez1'));
    }

    public function test__getNamedPresetOptionsOrFail()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);
        $this->assertSame(['FOO1'], $object->getNamedPresetOptionsOrFail('Class\\Foo', 'foo1'));
        $this->assertSame(['BAR1'], $object->getNamedPresetOptionsOrFail('Class\\Bar', 'bar1'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('no such option');
        $object->getNamedPresetOptionsOrFail('Class\\Bar', 'bar2');
    }

    public function test__getNamedPresetOptionsOrFail__isSingleton()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Class\\Geez" is a singleton ldap preset');
        $object->getNamedPresetOptionsOrFail('Class\\Geez', 'geez1');
    }

    public function test__getNamedPresetOptionsOrFail__notAPresetClass()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Fake\\Geez" is not a supported ldap preset class');
        $object->getNamedPresetOptionsOrFail('Fake\\Geez', 'geez2');
    }

    public function test__getSingletonPresetOptions()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'geez' => ['GEEZ']]);
        $this->assertSame(['GEEZ'], $object->getSingletonPresetOptions('Class\\Geez'));
        $this->assertNull($object->getSingletonPresetOptions('Fake\\Geez'));
        // it returns null for named preset
        $this->assertNull($object->getSingletonPresetOptions('Class\\Foo', 'foo1'));
    }

    public function test__getSingletonPresetOptionsOrFail()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'geez' => ['GEEZ']]);
        $this->assertSame(['GEEZ'], $object->getSingletonPresetOptionsOrFail('Class\\Geez'));

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Fake\\Geez" is not a supported ldap preset class');
        $object->getSingletonPresetOptionsOrFail('Fake\\Geez');
    }

    public function test__getSingletonPresetOptionsOrFail__isNotSingleton()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'geez' => ['GEEZ']]);

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Class\\Foo" is not a singleton ldap preset');
        $object->getSingletonPresetOptionsOrFail('Class\\Foo');
    }

    public function test__getNamedPresetsNames()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1'], 'foo2' => ['FOO2']],
                                           'bars' => ['bar1' => ['BAR1']]]);

        $this->assertSame(['foo1', 'foo2'], $object->getNamedPresetsNames('Class\\Foo'));
        $this->assertSame(['bar1'], $object->getNamedPresetsNames('Class\\Bar'));

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Fake\\Geez" is not a supported ldap preset class');
        $object->getNamedPresetsNames('Fake\\Geez');
    }

    public function test__getNamedPreset__withString()
    {
        $unsetMethods = [
            'getNamedPreset',
            'getNamedPresetByName',
            'createNamedPreset'
        ];
        $object = $this->getMockBuilder(HasPresets::class)
                       ->setMethods($this->allTraitMethodsExcept($unsetMethods)) // setMethodsExcept() didn't work ...
                       ->getMockForTrait();

        // expect no methods other than these two
        $allowedMethods = array_merge($unsetMethods, [
                'getNamedPresetOptionsOrFail',
                'cretePresetWithOptions'
        ]);
        $this->preventCallingTraitMethdosExcept($object, $allowedMethods);

        $object->expects($this->once())
               ->method('getNamedPresetOptionsOrFail')
               ->with('Class\\Foo', 'foo1')
               ->willReturn(['foo1opt1' => 'FOO1OPT1']);

        $preset = $this->createStub(PresetInterface::class);

        $object->expects($this->once())
               ->method('createPresetWithOptions')
               ->with('Class\\Foo', ['foo1opt1' => 'FOO1OPT1'])
               ->willReturn($preset);

        $this->assertSame($preset, $object->getNamedPreset('Class\\Foo', 'foo1'));
        $this->assertSame($preset, $object->getNamedPreset('Class\\Foo', 'foo1'));
    }

    public function test__getNamedPreset__withArray()
    {
        $unsetMethods = [
            'getNamedPreset',
            'createNamedPreset'
        ];
        $object = $this->getMockBuilder(HasPresets::class)
                       ->setMethods($this->allTraitMethodsExcept($unsetMethods)) // setMethodsExcept() didn't work ...
                       ->getMockForTrait();

        // expect no methods other than these two
        $allowedMethods = array_merge($unsetMethods, [
                'cretePresetWithOptions'
        ]);
        $this->preventCallingTraitMethdosExcept($object, $allowedMethods);

        $preset = $this->createStub(PresetInterface::class);

        $object->expects($this->exactly(2))
               ->method('createPresetWithOptions')
               ->with('Class\\Foo', ['foo1opt1' => 'FOO1OPT1'])
               ->willReturn($preset);

        $this->assertSame($preset, $object->getNamedPreset('Class\\Foo', ['foo1opt1' => 'FOO1OPT1']));
        $this->assertSame($preset, $object->getNamedPreset('Class\\Foo', ['foo1opt1' => 'FOO1OPT1']));
    }

    public function test__getSingletonPreset__withoutOptions()
    {
        $unsetMethods = [
            'getSingletonPreset',
            'createSingletonPreset'
        ];
        $object = $this->getMockBuilder(HasPresets::class)
                       ->setMethods($this->allTraitMethodsExcept($unsetMethods)) // setMethodsExcept() didn't work ...
                       ->getMockForTrait();

        // expect no methods other than these two
        $allowedMethods = array_merge($unsetMethods, [
                'getSingletonPresetOptionsOrFail',
                'cretePresetWithOptions'
        ]);
        $this->preventCallingTraitMethdosExcept($object, $allowedMethods);

        $object->expects($this->once())
               ->method('getSingletonPresetOptionsOrFail')
               ->with('Class\\Geez')
               ->willReturn(['geezopt1' => 'GEEZOPT1']);

        $preset = $this->createStub(PresetInterface::class);

        $object->expects($this->once())
               ->method('createPresetWithOptions')
               ->with('Class\\Geez', ['geezopt1' => 'GEEZOPT1'])
               ->willReturn($preset);

        $this->assertSame($preset, $object->getSingletonPreset('Class\\Geez'));
        $this->assertSame($preset, $object->getSingletonPreset('Class\\Geez'));
    }

    public function test__getSingletonPreset__withOptions()
    {
        $unsetMethods = [
            'getSingletonPreset',
            'createSingletonPreset'
        ];
        $object = $this->getMockBuilder(HasPresets::class)
                       ->setMethods($this->allTraitMethodsExcept($unsetMethods)) // setMethodsExcept() didn't work ...
                       ->getMockForTrait();

        // expect no methods other than these two
        $allowedMethods = array_merge($unsetMethods, [
                'cretePresetWithOptions'
        ]);
        $this->preventCallingTraitMethdosExcept($object, $allowedMethods);

        $preset = $this->createStub(PresetInterface::class);

        $object->expects($this->exactly(2))
               ->method('createPresetWithOptions')
               ->with('Class\\Geez', ['geezopt1' => 'FOO1OPT1'])
               ->willReturn($preset);

        $this->assertSame($preset, $object->getSingletonPreset('Class\\Geez', ['geezopt1' => 'FOO1OPT1']));
        $this->assertSame($preset, $object->getSingletonPreset('Class\\Geez', ['geezopt1' => 'FOO1OPT1']));
    }
}
