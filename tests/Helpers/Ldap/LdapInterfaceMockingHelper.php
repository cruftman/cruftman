<?php
/**
 * @file tests/Helpers/Ldap/LdapInterfaceMockingHelper.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Tests\Helpers\Ldap;

use Korowai\Lib\Ldap\LdapInterface;
use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking LdapInterface.
 */
trait LdapInterfaceMockingHelper
{
    use MockingHelper;
    use BindingInterfaceMockingHelper;
    use EntryManagerInterfaceMockingHelper;
    use AdapterInterfaceMockingHelper;

    public function getLdapInterfaceMockMethods()
    {
        return ['getAdapter'];
    }

    public function configureLdapInterfaceMock(LdapInterface $mock, array $config)
    {
        $this->configureBindingInterfaceMock($mock, $config);
        $this->configureEntryManagerInterfaceMock($mock, $config);
        $this->configureAdapterInterfaceMock($mock, $config);

        $methods = $this->getLdapInterfaceMockMethods();
        $this->configureMock($mock, $methods, $config);
    }
}

// vim: syntax=php sw=4 ts=4 et:
