<?php
/**
 * @file src/Cruftman/Ldap/LdapServiceInterface.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\contracts
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Korowai\Lib\Ldap\LdapInterface;

interface LdapServiceInterface
{
    /**
     * Returns an array of LDAP connection names as defined in ldap config.
     *
     * @return string[]
     */
    public function getSessionNames() : array;

    /**
     * Returns an LDAP connections.
     *
     * @param string $name Connection name; one of the strings returned by getSessionNames().
     * @return LdapInterface
     */
    public function getSession(string $name) : LdapInterface;
}

// vim: syntax=php sw=4 ts=4 et:
