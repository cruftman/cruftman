<?php

namespace Tests\Unit\Ldap\Traits;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Traits\HasPresets;
use Illuminate\Support\Arr;
use Cruftman\Support\Exceptions\OptionNotFoundException;

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
                    'Class\\Bar' => 'bars'
                ];
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

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unsupported Preset class "Fake\\Geez"');
        $object->getPresetOptionsKeyOrFail('Fake\\Geez');
    }

    public function test__getPresetOptions()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);
        $this->assertSame(['FOO1'], $object->getPresetOptions('Class\\Foo', 'foo1'));
        $this->assertSame(['BAR1'], $object->getPresetOptions('Class\\Bar', 'bar1'));
        $this->assertNull($object->getPresetOptions('Class\\Bar', 'bar2'));
        $this->assertNull($object->getPresetOptions('Fake\\Geez', 'geez1'));
    }

    public function test__getPresetOptionsOrFail()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);
        $this->assertSame(['FOO1'], $object->getPresetOptionsOrFail('Class\\Foo', 'foo1'));
        $this->assertSame(['BAR1'], $object->getPresetOptionsOrFail('Class\\Bar', 'bar1'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('no such option');
        $object->getPresetOptionsOrFail('Class\\Bar', 'bar2');
    }

    public function test__getPresetOptionsOrFail__2()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1']], 'bars' => ['bar1' => ['BAR1']]]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unsupported Preset class "Fake\\Geez"');
        $object->getPresetOptionsOrFail('Fake\\Geez', 'geez2');
    }

    public function test__getPresets()
    {
        $object = $this->createTestObject(['foos' => ['foo1' => ['FOO1'], 'foo2' => ['FOO2']],
                                           'bars' => ['bar1' => ['BAR1']]]);

        $this->assertSame(['foo1', 'foo2'], $object->getPresets('Class\\Foo'));
        $this->assertSame(['bar1'], $object->getPresets('Class\\Bar'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unsupported Preset class "Fake\\Geez"');
        $object->getPresets('Fake\\Geez');
    }

    public function test__getPreset()
    {
        // TODO: implement
    }
}
