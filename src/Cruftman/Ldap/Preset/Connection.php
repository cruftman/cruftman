<?php
/**
 * @file src/Cruftman/Ldap/Preset/Connection.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Korowai\Lib\Ldap\Ldap;
use Korowai\Lib\Ldap\LdapInterface;
use Cruftman\Support\PresetAbstract;

/**
 * Parametrized LDAP connection.
 */
class Connection extends PresetAbstract
{
    /**
     * Creates and returns LDAP instance using on connection options.
     *
     * @param  array $arguments
     * @return \Korowai\Lib\Ldap\LdapInterface
     */
    public function createLdap(array $arguments = []) : LdapInterface
    {
        $options = $this->substOptions($arguments);
        return Ldap::createWithConfig($options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
