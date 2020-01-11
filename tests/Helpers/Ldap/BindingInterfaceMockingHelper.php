<?php
/**
 * @file tests/Helpers/Ldap/BindingInterfaceMockingHelper.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Tests\Helpers\Ldap;

use Korowai\Lib\Ldap\Adapter\BindingInterface;
use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking BindingInterface.
 */
trait BindingInterfaceMockingHelper
{
    use MockingHelper;

    public function getBindingInterfaceMockMethods()
    {
        return ['isBound', 'bind', 'unbind'];
    }

    public function configureBindingInterfaceMock(BindingInterface $mock, array $config)
    {
        $methods = $this->getBindingInterfaceMockMethods();
        $this->configureMock($mock, $methods, $config);
    }
}

// vim: syntax=php sw=4 ts=4 et:
