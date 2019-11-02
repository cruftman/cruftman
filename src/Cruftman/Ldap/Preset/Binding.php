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
        $dn = $this->substOptionOrFail('0', $arguments);
        $pw = $this->substOptionOrFail('1', $arguments);
        return $ldap->bind($dn, $pw);
    }
}

// vim: syntax=php sw=4 ts=4 et:
