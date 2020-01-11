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

use Korowai\Lib\Ldap\Adapter\AdapterInterface;
use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking AdapterInterface.
 */
trait AdapterInterfaceMockingHelper
{
    use MockingHelper;

    protected function getAdapterInterfaceMockMethods()
    {
        return ['getBinding', 'getEntryManager', 'createSearchQuery', 'createCompareQuery'];
    }

    protected function configureAdapterInterfaceMock(AdapterInterface $mock, array $config)
    {
        $methods = $this->getAdapterInterfaceMockMethods();
        $this->configureMock($mock, $methods, $config);
    }
}

// vim: syntax=php sw=4 ts=4 et:
