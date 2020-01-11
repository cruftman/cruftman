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

use Cruftman\Ldap\Tools\Connector;
use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking Tool.
 */
trait ConnectorMockingHelper
{
    use MockingHelper;

    protected function getConnectorMockMethods()
    {
        return ['createLdap'];
    }

    protected function configureConnectorMock(Connector $mock, array $config)
    {
        $methods = $this->getConnectorMockMethods();
        $this->configureMock($mock, $methods, $config);
    }

    protected function createConnectorMock(array $config)
    {
        $mock = $this->getMockBuilder(Connector::class)->getMock();
        $this->configureConnectorMock($mock, $config);
        return $mock;
    }
}

// vim: syntax=php sw=4 ts=4 et:
