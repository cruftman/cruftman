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
 * Add a protected attribute named *$ldapInterface* and geLdapInterface()/setLdapInterface() accessors.
 *
 * If
 */
trait HasLdapInterface
{
    /**
     * @var LdapInterface
     */
    protected $ldapInterface;

    /**
     * Sets $ldapInterface to the object.
     *
     * @param  LdapInterface|null $ldapInterface
     * @return object $this
     */
    public function setLdapInterface(?LdapInterface $ldapInterface)
    {
        $this->ldapInterface = $ldapInterface;
        return $this;
    }

    /**
     * Returns the encapsulated *LdapInterface* instance.
     *
     * If the instance was not set to this end and ``createLdapInstance()``
     * method exists, the method gets called to create new instance of the
     * *LdapInterface*.
     *
     * @return LdapInterface|null
     */
    public function getLdapInterface() : ?LdapInterface
    {
        if (!isset($this->ldapInterface) && method_exists($this, 'createLdapInterface')) {
            $this->ldapInterface = $this->createLdapInterface();
        }
        return $this->ldapInterface;
    }
}

// vim: syntax=php sw=4 ts=4 et:
