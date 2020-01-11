<?php
/**
 * @file tests/Helpers/Ldap/EntryManagerInterfaceMockingHelper.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Tests\Helpers\Ldap;

use Korowai\Lib\Ldap\Adapter\EntryManagerInterface;
use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking EntryManagerInterface.
 */
trait EntryManagerInterfaceMockingHelper
{
    use MockingHelper;

    protected function getEntryManagerInterfaceMockMethods()
    {
        return ['add', 'rename', 'delete'];
    }

    protected function configureEntryManagerInterfaceMock(EntryManagerInterface $mock, array $config)
    {
        $methods = $this->getEntryManagerInterfaceMockMethods();
        $this->configureMock($mock, $methods, $config);
    }
}

// vim: syntax=php sw=4 ts=4 et:
