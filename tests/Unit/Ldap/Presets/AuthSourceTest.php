<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\AuthSource;
use Cruftman\Ldap\Presets\AuthAttempt;
use Cruftman\Ldap\Presets\Session;
use Cruftman\Ldap\Presets\Search;
use Cruftman\Ldap\Presets\Aggregate;
use Cruftman\Support\Preset;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;


class AuthSourceTest extends TestCase
{
    public function test__extends__Preset()
    {
        $parents = class_parents(AuthSource::class);
        $this->assertContains(Preset::class, $parents);
    }

    public function test__attempt__missing()
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('"attempt"');
        new AuthSource([]);
    }

    public function test__sessions__validation()
    {
        $source = new AuthSource(['attempt' => [], 'sessions' => []]);

        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('option "sessions" with value "FOO"');
        new AuthSource(['attempt' => [], 'sessions' => 'FOO']);
    }

    public function test__search__validation()
    {
        $source = new AuthSource(['attempt' => [], 'sessions' => [], 'search' => []]);

        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "sessions" is missing (required by "search" option)');
        new AuthSource(['attempt' => [], 'search' => []]);
    }

    public function test__locate__validation()
    {
        $source = new AuthSource(['attempt' => [], 'sessions' => [], 'locate' => []]);

        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "sessions" is missing (required by "locate" option)');
        new AuthSource(['attempt' => [], 'locate' => []]);
    }

    public function test__attempt()
    {
        $source = new AuthSource(['attempt' => [ 'binding' => ['BIND'] ]], new Aggregate());
        $attempt = $source->attempt();
        $this->assertInstanceOf(AuthAttempt::class, $attempt);
        $this->assertSame(['binding' => ['BIND']], $attempt->getOptions()->getArrayCopy());
    }

    public function test__attempt__ref()
    {
        $presets = new Aggregate(['auth_attempts' => ['aat1' => ['binding' => ['BIND']]]]);
        $source = new AuthSource(['attempt' => 'aat1'], $presets);
        $attempt = $source->attempt();
        $this->assertInstanceOf(AuthAttempt::class, $attempt);
        $this->assertSame(['binding' => ['BIND']], $attempt->getOptions()->getArrayCopy());
    }

    public function test__sessions()
    {
        $sess1 = ['connection' => ['S1CONN'], 'binding' => ['S1BIND']];
        $sess2 = ['connection' => ['S2CONN'], 'binding' => ['S2BIND']];
        $presets = new Aggregate(['sessions' => ['sess2' => $sess2]]);

        $options = ['attempt' => [], 'sessions' => [$sess1, 'sess2']];
        $schema = new AuthSource($options, $presets);

        $sessions = $schema->sessions();
        $this->assertIsArray($sessions);
        $this->assertCount(2, $sessions);

        $this->assertInstanceOf(Session::class, $sessions[0]);
        $this->assertSame($sess1, $sessions[0]->getOptions()->getArrayCopy());
        $this->assertInstanceOf(Session::class, $sessions[1]);
        $this->assertSame($sess2, $sessions[1]->getOptions()->getArrayCopy());
    }

    public function test__search()
    {
        $srch1 = ['base' => 'ou=people,dc=example,dc=org', 'filter' => 'uid=*'];
        $source = new AuthSource(['attempt' => [], 'sessions' => [], 'search' => $srch1], new Aggregate());
        $search = $source->search();
        $this->assertInstanceOf(Search::class, $search);
        $this->assertSame($srch1, $search->getOptions()->getArrayCopy());
    }

    public function test__search__ref()
    {
        $srch1 = ['base' => 'ou=people,dc=example,dc=org', 'filter' => 'uid=*'];
        $presets = new Aggregate(['searches' => ['srch1' => $srch1]]);
        $source = new AuthSource(['attempt' => [], 'sessions' => [], 'search' => 'srch1'], $presets);
        $search = $source->search();
        $this->assertInstanceOf(Search::class, $search);
        $this->assertSame($srch1, $search->getOptions()->getArrayCopy());
    }

    public function test__locate()
    {
        $srch1 = ['base' => 'ou=people,dc=example,dc=org', 'filter' => 'uid=*'];
        $source = new AuthSource(['attempt' => [], 'sessions' => [], 'locate' => $srch1], new Aggregate());
        $locate = $source->locate();
        $this->assertInstanceOf(Search::class, $locate);
        $this->assertSame($srch1, $locate->getOptions()->getArrayCopy());
    }

    public function test__locate__ref()
    {
        $srch1 = ['base' => 'ou=people,dc=example,dc=org', 'filter' => 'uid=*'];
        $presets = new Aggregate(['searches' => ['srch1' => $srch1]]);
        $source = new AuthSource(['attempt' => [], 'sessions' => [], 'locate' => 'srch1'], $presets);
        $locate = $source->locate();
        $this->assertInstanceOf(Search::class, $locate);
        $this->assertSame($srch1, $locate->getOptions()->getArrayCopy());
    }
}
