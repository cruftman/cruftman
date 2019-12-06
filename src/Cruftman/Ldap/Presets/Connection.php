<?php
/**
 * @file src/Cruftman/Ldap/Presets/Connection.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Presets;

use Cruftman\Support\Preset;

/**
 * LDAP connection preset.
 */
class Connection extends Preset
{
    /**
     * Returns configuration array that may be passed to *Ldap::createWithConfig()*.
     *
     * @param array $arguments
     * @return array
     */
    public function ldapConfig(array $arguments = [])
    {
        return $this->substOptions($arguments);
    }
}

// vim: syntax=php sw=4 ts=4 et:
