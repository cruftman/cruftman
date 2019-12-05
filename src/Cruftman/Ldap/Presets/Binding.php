<?php
/**
 * @file src/Cruftman/Ldap/Presets/Binding.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Presets;

use Korowai\Lib\Ldap\Adapter\BindingInterface;
use Cruftman\Support\Preset;

/**
 * Binding Preset.
 */
class Binding extends Preset
{

    /**
     * Invokes ``bind()`` method on the *$ldap* object.
     *
     * @param  BindingInterface $ldap
     * @param  array $arguments
     * @return bool
     */
    public function bindLdapInterface(BindingInterface $ldap, array $arguments = [])
    {
        return $ldap->bind($this->getBindDn($arguments), $this->getBindPassword($arguments));
    }

    /**
     * Returns the DN option.
     *
     * @param  array $arguments
     * @return string
     */
    public function getBindDn(array $arguments = []) : string
    {
        return $this->substOptionOrFail('0', $arguments);
    }

    /**
     * Returns the password option.
     *
     * @param  array $arguments
     * @return string
     */
    public function getBindPassword(array $arguments = []) : string
    {
        return $this->substOptionOrFail('1', $arguments);
    }
}

// vim: syntax=php sw=4 ts=4 et:
