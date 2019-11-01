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
 * @todo Write documentation.
 */
class Binding extends AbstractPreset
{

    /**
     * Creates and returns LDAP instance using on connection options.
     *
     * @param  \Korowai\Lib\Ldap\Adapter\BindingInterface $ldap
     * @param  array $arguments
     * @return \Korowai\Lib\Ldap\LdapInterface
     */
    public function bindLdapInterface(BindingInterface $ldap, array $arguments = [])
    {
        $dn = $this->substOptionOrFail('0', $arguments);
        $pw = $this->substOptionOrFail('1', $arguments);
        return $ldap->bind($dn, $pw);
    }
}

// vim: syntax=php sw=4 ts=4 et:
