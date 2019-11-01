<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasLdapInterface.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Korowai\Lib\Ldap\LdapInterface;

/**
 * Add a protected attribute named *$ldapInterface* and getter/setter methods.
 */
trait HasLdapInterface
{
    /**
     * @var \Korowai\Lib\Ldap\LdapInterface
     */
    protected $ldapInterface;

    /**
     * Sets $ldapInterface to the object.
     *
     * @param  \Korowai\Lib\Ldap\LdapInterface $ldapInterface
     * @return object $this
     */
    public function setLdapInterface(LdapInterface $ldapInterface)
    {
        $this->ldapInterface = $ldapInterface;
        return $this;
    }

    /**
     * Returns the $ldapInterface.
     *
     * @return LdapInterface
     */
    public function getLdapInterface() : LdapInterface
    {
        return $this->ldapInterface;
    }
}

// vim: syntax=php sw=4 ts=4 et:
