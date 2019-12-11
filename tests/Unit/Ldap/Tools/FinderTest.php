<?php

namespace Tests\Unit\Ldap\Tools;

use PHPUnit\Framework\TestCase;

use Cruftman\Ldap\Tools\Finder;
use Cruftman\Ldap\Presets\Search;
use Korowai\Lib\Ldap\Adapter\AdapterInterface;
use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\ResultInterface;


class FinderTest extends TestCase
{
    protected function getMockedArgs(SearchQueryInterface $query)
    {
        $arguments = ['username' => 'jsmith', 'branch' => 'ou=people', 'options' => ['foo' => 'FOO']];
        $base = 'dc=example,dc=org';
        $filter = 'objectclass=*';
        $options = ['scope' => 'one'];

        $search = $this->createMock(Search::class);
        $search->expects($this->once())
               ->method('base')
               ->with($arguments)
               ->will($this->returnValue($arguments['branch'].','.$base));
        $search->expects($this->once())
               ->method('filter')
               ->with($arguments)
               ->will($this->returnValue('(&(uid='.$arguments['username'].')('.$filter.'))'));
        $search->expects($this->once())
               ->method('options')
               ->with($arguments)
               ->will($this->returnValue(array_merge($options, $arguments['options'])));

        $ldap = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $ldap->expects($this->once())
             ->method('createSearchQuery')
             ->with('ou=people,dc=example,dc=org', '(&(uid=jsmith)(objectclass=*))', ['scope'=>'one', 'foo'=>'FOO'])
             ->will($this->returnValue($query));
        return [$search, $ldap, $arguments];
    }

    public function test__createQuery()
    {
        $query = $this->createStub(SearchQueryInterface::class);
        [$search, $ldap, $arguments] = $this->getMockedArgs($query);

        $finder = new Finder;
        $this->assertSame($query, $finder->createQuery($search, $ldap, $arguments));
    }

    public function test__search()
    {
        $result = $this->createStub(ResultInterface::class);
        $query = $this->createMock(SearchQueryInterface::class);
        $query->expects($this->once())
              ->method('getResult')
              ->with()
              ->will($this->returnValue($result));
        [$search, $ldap, $arguments] = $this->getMockedArgs($query);
        $finder = new Finder;
        $this->assertSame($result, $finder->search($search, $ldap, $arguments));
    }
}
