<?php
/**
 * @file src/Cruftman/Ldap/Tools/Binder.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Tools;

use Cruftman\Ldap\Presets\Binding;
use Korowai\Lib\Ldap\Adapter\BindingInterface;

/**
 * Invokes *bind()* on a *BindingInterface* according to *Binding* preset.
 */
class Binder
{
    /**
     * Invokes *$ldap->bind($dn, $password)* with arguments taken from
     * *$binding* preset.
     *
     * @param  Binding $binding
     * @param  BindingInterface $ldap
     * @param  array $arguments
     * @return bool
     */
    public function bind(Binding $binding, BindingInterface $ldap, array $arguments) : bool
    {
        return $ldap->bind($binding->dn($arguments), $binding->password($arguments));
    }
}

// vim: syntax=php sw=4 ts=4 et:
