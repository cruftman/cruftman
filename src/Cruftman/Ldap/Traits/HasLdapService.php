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
 * Add a protected attribute named *$ldapService* and getLdapService()/setLdapService() accessors.
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
     * @param  Service $ldapService
     * @return object $this
     */
    public function setLdapService(Service $ldapService)
    {
        $this->ldapService = $ldapService;
        return $this;
    }

    /**
     * Returns the $ldapService.
     *
     * @return Service|null
     */
    public function getLdapService() : ?Service
    {
        return $this->ldapService;
    }
}

// vim: syntax=php sw=4 ts=4 et:
