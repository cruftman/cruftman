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

use Cruftman\Support\Preset;

/**
 * Binding Preset.
 */
class Binding extends Preset
{
    /**
     * Returns the DN option.
     *
     * @param  array $arguments
     * @return string
     */
    public function dn(array $arguments) : string
    {
        return $this->substOptionOrFail('0', $arguments);
    }

    /**
     * Returns the password option.
     *
     * @param  array $arguments
     * @return string
     */
    public function password(array $arguments) : string
    {
        return $this->substOptionOrFail('1', $arguments);
    }
}

// vim: syntax=php sw=4 ts=4 et:
