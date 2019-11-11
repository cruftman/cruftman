<?php
/**
 * @file src/Cruftman/Ldap/Preset/Binding.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Korowai\Lib\Ldap\Adapter\BindingInterface;

/**
 * Binding Preset.
 */
class Binding extends AbstractPreset
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
