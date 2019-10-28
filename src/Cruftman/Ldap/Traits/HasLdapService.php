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
use Cruftman\Ldap\SearchQueryTemplate;
use Cruftman\Ldap\AuthSource;
use Korowai\Lib\Ldap\LdapInterface;

/**
 * @todo Write documentation.
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

    /**
     * Returns a named instance of LdapInterface.
     *
     * @param  string $name
     * @return \Korowai\Lib\Ldap\LdapInterface
     */
    public function getLdapInstance(string $name) : LdapInterface
    {
        return $this->getLdapService()->getLdapInstance($name);
    }

    /**
     * Returns a named instance of SearchQueryTemplate.
     *
     * @param  string $name
     * @return \Cruftman\Ldap\SearchQueryTemplate
     */
    public function getSearchQuery(string $name) : SearchQueryTemplate
    {
        return $this->getLdapService()->getSearchQuery($name);
    }

    /**
     * Returns a named instance of AuthSource.
     *
     * @param  string $name
     * @return \Cruftman\Ldap\AuthSource
     */
    public function getAuthSource(string $name) : AuthSource
    {
        return $this->getLdapService()->getAuthSource($name);
    }
}

// vim: syntax=php sw=4 ts=4 et:
