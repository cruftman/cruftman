<?php
/**
 * @file tests/Helpers/MockingHelper.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Tests\Helpers\Ldap;

use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking LdapInterface.
 */
trait SearchQueryInterfaceMockingHelper
{
    use MockingHelper;

    protected function getSearchQueryInterfaceMockMethods()
    {
        return ['bind', 'createSearchQuery'];
    }

    protected function configureSearchQueryInterfaceMock(SearchQueryInterface $mock, array $config)
    {
        $methods = $this->getSearchQueryInterfaceMockMethods();
        $this->configureMock($mock, $methods, $config);
    }
}

// vim: syntax=php sw=4 ts=4 et:
