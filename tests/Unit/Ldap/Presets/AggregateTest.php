<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\Aggregate;

use Cruftman\Ldap\Presets\Connection;
use Cruftman\Ldap\Presets\Binding;
use Cruftman\Ldap\Presets\Session;
use Cruftman\Ldap\Presets\Search;
use Cruftman\Ldap\Presets\AuthAttempt;
use Cruftman\Ldap\Presets\AuthSource;
use Cruftman\Ldap\Presets\AuthSchema;

use Cruftman\Support\PresetsAggregateInterface;
use Cruftman\Support\OptionsInterface;
use Cruftman\Support\Traits\AggregatesPresets;
use Cruftman\Support\Traits\HasOptions;
use Cruftman\Support\Traits\ValidatesOptions;


class AggregateTest extends TestCase
{
    public function test__implements__PresetsAggregateInterface()
    {
        $interfaces = class_implements(Aggregate::class);
        $this->assertContains(PresetsAggregateInterface::class, $interfaces);
    }

    public function test__implements__OptionsInterface()
    {
        $interfaces = class_implements(Aggregate::class);
        $this->assertContains(OptionsInterface::class, $interfaces);
    }

    public function test__uses__AggregatesPresets()
    {
        $traits = class_uses(Aggregate::class);
        $this->assertContains(AggregatesPresets::class, $traits);
    }

    public function test__uses__HasOptions()
    {
        $traits = class_uses(Aggregate::class);
        $this->assertContains(HasOptions::class, $traits);
    }

    public function test__uses__ValidatesOptions()
    {
        $traits = class_uses(Aggregate::class);
        $this->assertContains(ValidatesOptions::class, $traits);
    }

    public function test__construct__withoutArgs()
    {
        $presets = new Aggregate();
        $this->assertSame([], $presets->getOptions());
        $this->assertSame("ldap", $presets->getOptionsPrefix());
    }

    public function test__construct__withOptions()
    {
        $options = [
            'connections' => [
                'cruftman' => [ 'uri' => 'ldap://cruftman.local' ]
            ],
        ];
        $presets = new Aggregate($options);
        $this->assertSame($options, $presets->getOptions());
        $this->assertSame("ldap", $presets->getOptionsPrefix());
    }

    public function test__construct__withOptionsAndPrefix()
    {
        $options = [
            'connections' => [
                'cruftman' => [ 'uri' => 'ldap://cruftman.local' ]
            ],
        ];
        $presets = new Aggregate($options, "foo");
        $this->assertSame($options, $presets->getOptions());
        $this->assertSame("foo", $presets->getOptionsPrefix());
    }

    public function test__getPresetClasses()
    {
        $classes = [
            Connection::class,
            Binding::class,
            Session::class,
            Search::class,
            AuthAttempt::class,
            AuthSource::class,
            AuthSchema::class,
        ];

        $this->assertSame($classes, (new Aggregate())->getPresetClasses());
    }

    public function test__isSingletonPreset()
    {
        $presets = new Aggregate();

        $this->assertFalse($presets->isSingletonPreset(Connection::class));
        $this->assertFalse($presets->isSingletonPreset(Binding::class));
        $this->assertFalse($presets->isSingletonPreset(Session::class));
        $this->assertFalse($presets->isSingletonPreset(Search::class));
        $this->assertFalse($presets->isSingletonPreset(AuthAttempt::class));
        $this->assertFalse($presets->isSingletonPreset(AuthSource::class));

        $this->assertTrue($presets->isSingletonPreset(AuthSchema::class));
    }
}
