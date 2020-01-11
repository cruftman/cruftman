<?php
/**
 * @file tests/Helpers/Ldap/AdapterInterfaceMockingHelper.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Tests\Helpers\Ldap;

use Korowai\Lib\Ldap\Adapter\AdapterInterface;
use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\CompareQueryInterface;
use Korowai\Lib\Ldap\Adapter\ResultInterface;
use Korowai\Lib\Ldap\Entry;

use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking AdapterInterface.
 */
trait AdapterInterfaceMockingHelper
{
    use MockingHelper;
    use SearchQueryInterfaceMockingHelper;
    use ResultInterfaceMockingHelper;

    protected function getAdapterInterfaceMockMethods()
    {
        return ['getBinding', 'getEntryManager', 'createSearchQuery', 'createCompareQuery'];
    }

    protected function configureAdapterInterfaceMock(AdapterInterface $mock, array $config)
    {
        $methods = $this->getAdapterInterfaceMockMethods();

        if (array_key_exists('searchQueries', $config)) {
            $this->configureAdapterInterfaceMockSearchQueries($mock, $config['searchQueries']);
        }
        $this->configureMock($mock, $methods, $config);
    }

    protected function configureAdapterInterfaceMockSearchQueries(AdapterInterface $mock, $searchQueries)
    {
        foreach ($searchQueries as $searchQuery) {
            $this->configureAdapterInterfaceMockSearchQuery($mock, $searchQuery);
        }
    }

    protected function configureAdapterInterfaceMockSearchQuery(AdapterInterface $mock, $searchQuery)
    {
        if (is_array($searchQuery)) {
            $params = $searchQuery[0];
            $entries = $searchQuery[1];

            $resultMock = $this->getMockBuilder(ResultInterface::class)->getMock();
            $this->configureResultInterfaceMock($resultMock, ['entries' => $entries]);

            $queryMock = $this->getMockBuilder(SearchQueryInterface::class)->getMock();
            $this->configureSearchQueryInterfaceMock($queryMock, /* TODO: ... */);
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
