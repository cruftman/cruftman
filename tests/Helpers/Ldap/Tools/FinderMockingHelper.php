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

namespace Tests\Helpers\Ldap\Tools;

use Cruftman\Ldap\Tools\Finder;
use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking Tool.
 */
trait FinderMockingHelper
{
    use MockingHelper;

    protected function getFinderMockMethods()
    {
        return ['createQuery', 'search'];
    }

    protected function configureFinderMock(Finder $mock, array $config)
    {
        $methods = $this->getFinderMockMethods();
        $this->configureMock($mock, $methods, $config);
    }

    protected function createFinderMock(array $config)
    {
        $mock = $this->getMockBuilder(Finder::class)->getMock();
        $this->configureFinderMock($mock, $config);
        return $mock;
    }
}

// vim: syntax=php sw=4 ts=4 et:
