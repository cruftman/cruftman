<?php
/**
 * @file src/Cruftman/Ldap/BindingTemplate.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Ldap\Traits\HasLdapService;
use Korowai\Lib\Ldap\Adapter\BindingInterface;

/**
 * Parametrized LDAP connection.
 */
class BindingTemplate
{
    use HasTemplateOptions,
        HasLdapService;

    /**
     * Initializes the service object.
     *
     * @param Service $ldap ldap service
     * @param array $templateOptions
     */
    public function __construct(Service $ldapService, array $options)
    {
        $this->setLdapService($ldapService);
        $this->setOptions($options);
    }

    /**
     * Creates and returns LDAP instance using on connection options.
     *
     * @param  \Korowai\Lib\Ldap\Adapter\BindingInterface $ldap
     * @param  array $arguments
     * @return \Korowai\Lib\Ldap\LdapInterface
     */
    public function bindLdapInstance(BindingInterface $ldap, array $arguments = [])
    {
        $dn = $this->substOptionOrFail('0', $arguments);
        $pw = $this->substOptionOrFail('1', $arguments);
        return $ldap->bind($dn, $pw);
    }
}

// vim: syntax=php sw=4 ts=4 et:
