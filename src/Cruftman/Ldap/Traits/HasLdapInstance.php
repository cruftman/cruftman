<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasLdapInstance.php
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
 * @todo Write documentation.
 */
trait HasLdapInstance
{
    /**
     * @var \Korowai\Lib\Ldap\LdapInterface
     */
    protected $ldapInstance;

    /**
     * Sets $ldapInstance to the object.
     *
     * @param  \Korowai\Lib\Ldap\LdapInterface $ldapInstance
     * @return $this
     */
    public function setLdapInstance(LdapInterface $ldapInstance)
    {
        $this->ldapInstance = $ldapInstance;
        return $this;
    }

    /**
     * Returns the $ldapInstance.
     *
     * @return array
     */
    public function getLdapInstance() : LdapInterface
    {
        return $this->ldapInstance;
    }
}

// vim: syntax=php sw=4 ts=4 et:
