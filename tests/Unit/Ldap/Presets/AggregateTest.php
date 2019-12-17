<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\Aggregate;

use Cruftman\Ldap\Presets\Connection;
use Cruftman\Ldap\Presets\Binding;
use Cruftman\Ldap\Presets\Session;
use Cruftman\Ldap\Presets\Search;
use Cruftman\Ldap\Presets\BindSearch;
use Cruftman\Ldap\Presets\AuthAttempt;
use Cruftman\Ldap\Presets\AuthSource;
use Cruftman\Ldap\Presets\AuthSchema;

use Cruftman\Support\PresetsAggregateInterface;
use Cruftman\Support\OptionsInterface;
use Cruftman\Support\Traits\AggregatesPresets;
use Cruftman\Support\Traits\HasOptions;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Support\Exceptions\OptionNotFoundException;


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
            BindSearch::class,
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

    public function test__connections()
    {
        $presets = new Aggregate(['connections' => ['conn1' => [], 'conn2' => []]]);
        $this->assertSame(['conn1', 'conn2'], $presets->connections());
    }

    public function test__bindings()
    {
        $presets = new Aggregate(['bindings' => ['bind1' => [], 'bind2' => []]]);
        $this->assertSame(['bind1', 'bind2'], $presets->bindings());
    }

    public function test__sessions()
    {
        $presets = new Aggregate(['sessions' => ['sess1' => [], 'sess2' => []]]);
        $this->assertSame(['sess1', 'sess2'], $presets->sessions());
    }

    public function test__searches()
    {
        $presets = new Aggregate(['searches' => ['srch1' => [], 'srch2' => []]]);
        $this->assertSame(['srch1', 'srch2'], $presets->searches());
    }

    public function test__authAttempts()
    {
        $presets = new Aggregate(['auth_attempts' => ['aat1' => [], 'aat2' => []]]);
        $this->assertSame(['aat1', 'aat2'], $presets->authAttempts());
    }

    public function test__authSources()
    {
        $presets = new Aggregate(['auth_sources' => ['asrc1' => [], 'asrc2' => []]]);
        $this->assertSame(['asrc1', 'asrc2'], $presets->authSources());
    }

    public function test__getNamedPresetNames__withEmptyConfig()
    {
        $presets = new Aggregate([]);
        $this->assertSame([], $presets->connections());
        $this->assertSame([], $presets->bindings());
        $this->assertSame([], $presets->searches());
        $this->assertSame([], $presets->authAttempts());
        $this->assertSame([], $presets->authSources());
    }

    public function test__connection()
    {
        $config = [
            'connections' => [
                'ldap1' => [ 'uri' => 'ldap/ldap1.example.org' ],
                'ldap2' => [ 'uri' => 'ldap/ldap2.example.org' ],
            ]
        ];
        $presets = new Aggregate($config);

        $conn1 = $presets->connection('ldap1');
        $conn2 = $presets->connection('ldap2');
        $this->assertInstanceOf(Connection::class, $conn1);
        $this->assertInstanceOf(Connection::class, $conn2);
        $this->assertSame($config['connections']['ldap1'], $conn1->getOptions()->getArrayCopy());
        $this->assertSame($config['connections']['ldap2'], $conn2->getOptions()->getArrayCopy());
    }

    public function test__connection__throwsOptionNotFoundException__1()
    {
        $config = [];
        $presets = new Aggregate($config);

        $this->expectException(OptionNotFoundException::class);
        $this->expectExceptionMessage('option "ldap.connections.ldap1" not found');
        $presets->connection('ldap1');
    }

    public function test__connection__throwsOptionNotFoundException__2()
    {
        $config = ['connections' => []];
        $presets = new Aggregate($config);

        $this->expectException(OptionNotFoundException::class);
        $this->expectExceptionMessage('option "ldap.connections.ldap1" not found');
        $presets->connection('ldap1');
    }

    public function test__binding()
    {
        $config = [
            'bindings' => [
                'bind1' => [ 'uid=user1,dc=example,dc=org' , 'pass1' ],
                'bind2' => [ 'uid=user2,dc=example,dc=org' , 'pass2' ],
            ],
        ];
        $presets = new Aggregate($config);

        $bind1 = $presets->binding('bind1');
        $bind2 = $presets->binding('bind2');

        $this->assertInstanceOf(Binding::class, $bind1);
        $this->assertInstanceOf(Binding::class, $bind2);
        $this->assertSame($config['bindings']['bind1'], $bind1->getOptions()->getArrayCopy());
        $this->assertSame($config['bindings']['bind2'], $bind2->getOptions()->getArrayCopy());
    }

    public function test__session()
    {
        $config = [
            'sessions' => [
                'bind1@ldap1' => [ 'binding' => 'bind1', 'connection' => 'ldap1' ],
            ]
        ];

        $presets = new Aggregate($config);
        $sess = $presets->session('bind1@ldap1');
        $this->assertInstanceOf(Session::class, $sess);
        $this->assertSame($config['sessions']['bind1@ldap1'], $sess->getOptions()->getArrayCopy());
    }

    public function test__search()
    {
        $config = [
            'searches' => [
                'people' => [
                    'base' => 'ou=people,dc=example,dc=org',
                    'filter' => '(uid=*)',
                    'options' => ['scope' => 'one']
                ],
            ],
        ];
        $presets = new Aggregate($config);
        $search = $presets->search('people');
        $this->assertInstanceOf(Search::class, $search);
        $this->assertSame($config['searches']['people'], $search->getOptions()->getArrayCopy());
    }

    public function test__authAttempt()
    {
        $config = [
            'auth_attempts' => [
                'aat1' => [
                    'binding' => ['${dn}', '${password}']
                ],
            ],
        ];
        $presets = new Aggregate($config);
        $aat1 = $presets->authAttempt('aat1');
        $this->assertInstanceOf(AuthAttempt::class, $aat1);
        $this->assertSame($config['auth_attempts']['aat1'], $aat1->getOptions()->getArrayCopy());
    }

    public function test__authSource()
    {
        $config = [
            'auth_sources' => [
                'asrc1' => [
                    'attempt' => [
                        'connections' => ['ldap1', 'ldap2'],
                        'binding' => ['uid=${username},ou=people,dc=example,dc=org', '${password}'],
                        'filter' => '(&(accountStatus=enabled))',
                    ],
                ],
            ],
        ];
        $presets = new Aggregate($config);
        $asrc1 = $presets->authSource('asrc1');
        $this->assertInstanceOf(AuthSource::class, $asrc1);
        $this->assertSame($config['auth_sources']['asrc1'], $asrc1->getOptions()->getArrayCopy());
    }

    public function test__authSchema()
    {
        $config = [
            'auth_schema' => [
                'arguments' => [],
                'sources' => [ 'asrc1' ],
            ],
        ];
        $presets = new Aggregate($config);
        $schema = $presets->authSchema();
        $this->assertInstanceOf(AuthSchema::class, $schema);
        $this->assertSame($config['auth_schema'], $schema->getOptions()->getArrayCopy());
    }
}
