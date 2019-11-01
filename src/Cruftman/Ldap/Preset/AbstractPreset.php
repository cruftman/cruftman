<?php
/**
 * @file src/Cruftman/Ldap/Preset/AbstractPreset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Ldap\Service;
use Cruftman\Ldap\Traits\HasLdapService;

/**
 * Abstract base class for presets.
 */
class AbstractPreset
{
    use HasTemplateOptions,
        HasLdapService;

    /**
     * Initializes the Ldap object.
     *
     * @param  Service $ldapService
     * @param  array $options
     */
    public function __construct(Service $ldapService, array $options)
    {
        $this->setLdapService($ldapService);
        $this->setOptions($options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
