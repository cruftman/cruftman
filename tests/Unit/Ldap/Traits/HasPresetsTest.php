<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasPresets;
use Cruftman\Ldap\Preset\PresetInterface;
use Cruftman\Ldap\Exceptions\PresetException;
use Cruftman\Support\Exceptions\OptionNotFoundException;
use Illuminate\Support\Arr;

class HasPresetsTest extends TestCase
{
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
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);
        $this->assertSame(['FOO1'], $object->getNamedPresetOptions('Class\\Foo', 'foo1'));
        $this->assertSame(['BAR1'], $object->getNamedPresetOptions('Class\\Bar', 'bar1'));
        $this->assertNull($object->getNamedPresetOptions('Class\\Bar', 'bar2'));
        $this->assertNull($object->getNamedPresetOptions('Fake\\Geez', 'geez1'));
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

    public function test__getNamedPresetNames()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1'], 'foo2' => ['FOO2']],
                                           'bars' => ['bar1' => ['BAR1']]]);

        $this->assertSame(['foo1', 'foo2'], $object->getNamedPresetNames('Class\\Foo'));
        $this->assertSame(['bar1'], $object->getNamedPresetNames('Class\\Bar'));

        $this->expectException(PresetException::class);
        $this->expectExceptionMessage('"Fake\\Geez" is not a supported ldap preset class');
        $object->getNamedPresetNames('Fake\\Geez');
    }

    public function test__getPreset()
    {
        // TODO: implement
    }
}
