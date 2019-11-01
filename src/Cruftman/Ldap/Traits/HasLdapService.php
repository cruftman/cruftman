<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasLdapService.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Service;

/**
 * Add a protected attribute named *$ldapService* and getter/setter methods.
 */
trait HasLdapService
{
    /**
     * @var \Cruftman\Ldap\Service
     */
    protected $ldapService;

    /**
     * Sets $ldapService to the object.
     *
     * @param  \Cruftman\Ldap\Service $ldapService
     * @return $this
     */
    public function setLdapService(Service $ldapService)
    {
        $this->ldapService = $ldapService;
        return $this;
    }

    /**
     * Returns the $ldapService.
     *
     * @return array
     */
    public function getLdapService() : Service
    {
        return $this->ldapService;
    }
}

// vim: syntax=php sw=4 ts=4 et:
