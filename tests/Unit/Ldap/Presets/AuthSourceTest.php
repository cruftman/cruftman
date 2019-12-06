<?php

namespace Tests\Unit\Ldap\Presets;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Presets\AuthSource;
//use Cruftman\Ldap\Presets\Connection;
//use Cruftman\Ldap\Presets\Binding;
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

//    public function test__sources()
//    {
//        $options = ['sources' => [['attempt' => ['ATT']], 'src2']];
//        $schema = new AuthSource($options, new Aggregate(['auth_sources' => [
//                'src2' => ['attempt' => ['SRC2ATT']]
//        ]]));
//
//        $sources = $schema->sources();
//        $this->assertIsArray($sources);
//        $this->assertCount(2, $sources);
//
//        $this->assertInstanceOf(AuthSource::class, $sources[0]);
//        $this->assertSame(['attempt' => ['ATT']], $sources[0]->getOptions()->getArrayCopy());
//        $this->assertInstanceOf(AuthSource::class, $sources[1]);
//        $this->assertSame(['attempt' => ['SRC2ATT']], $sources[1]->getOptions()->getArrayCopy());
//    }
//
//    public function test__ambiguous__validation()
//    {
//        $this->assertInstanceOf(AuthSource::class, new AuthSource(['sources' => [], 'ambiguous' => 'first']));
//        $this->assertInstanceOf(AuthSource::class, new AuthSource(['sources' => [], 'ambiguous' => 'each']));
//        $this->assertInstanceOf(AuthSource::class, new AuthSource(['sources' => [], 'ambiguous' => 'fail']));
//
//        $this->expectException(InvalidOptionsException::class);
//        $this->expectExceptionMessage('"ambiguous" with value "xyz"');
//        new AuthSource(['sources' => [], 'ambiguous' => 'xyz']);
//    }
//
//    public function test__validation()
//    {
//        new AuthSource(['sources' => [], 'arguments' => []]);
//        new AuthSource(['sources' => [], 'arguments' => ['useruuid' => 'UUID']]);
//        new AuthSource(['sources' => [], 'arguments' => ['username' => 'NAME']]);
//        new AuthSource(['sources' => [], 'arguments' => ['password' => 'PASS']]);
//
//        $this->expectException(UndefinedOptionsException::class);
//        $this->expectExceptionMessage('option "xyz" does not exist');
//        new AuthSource(['sources' => [], 'arguments' => ['xyz' => 'XYZ']]);
//    }
//
//    public function test__ambiguous()
//    {
//        $options = ['sources' => [], 'ambiguous' => 'first'];
//        $schema = new AuthSource($options);
//
//        $this->assertSame('first', $schema->ambiguous());
//        $this->assertSame('first', $schema->ambiguous('xyz'));
//    }
//
//    public function test__ambiguous__null()
//    {
//        $options = ['sources' => []];
//        $schema = new AuthSource($options);
//
//        $this->assertNull($schema->ambiguous());
//    }
//
//    public function test__ambiguous__default()
//    {
//        $options = ['sources' => []];
//        $schema = new AuthSource($options);
//
//        $this->assertSame('xyz', $schema->ambiguous('xyz'));
//    }
//
//    public function test__arguments()
//    {
//        $options = [
//            'sources' => [],
//            'arguments' => [
//                'useruuid' => 'entryuuid',
//                'username' => 'uid',
//                'password' => 'password'
//            ],
//        ];
//        $schema = new AuthSource($options);
//
//        $arguments = $schema->arguments();
//        $this->assertSame($options['arguments'], $schema->arguments());
//        $this->assertSame($options['arguments'], $schema->arguments(['xyz']));
//    }
}
