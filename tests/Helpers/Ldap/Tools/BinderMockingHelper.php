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

use Cruftman\Ldap\Tools\Binder;
use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking Tool.
 */
trait BinderMockingHelper
{
    use MockingHelper;

    protected function getBinderMockMethods()
    {
        return ['bind', 'bindDn'];
    }

    protected function configureBinderMock(Binder $mock, array $config)
    {
        $methods = $this->getBinderMockMethods();
        $this->configureMock($mock, $methods, $config);
    }

    protected function createBinderMock(array $config)
    {
        $mock = $this->getMockBuilder(Binder::class)->getMock();
        $this->configureBinderMock($mock, $config);
        return $mock;
    }
}

// vim: syntax=php sw=4 ts=4 et:
