<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasAuthSourcePreset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Preset\AuthSource;

/**
 * Add a protected attribute named *$authSourcePreset* and public accessors.
 */
trait HasAuthSourcePreset
{
    /**
     * @var AuthSource
     */
    protected $authSourcePreset;

    /**
     * Sets AuthSource preset to the object.
     *
     * @param  AuthSource $preset
     * @return object $this
     */
    public function setAuthSourcePreset(AuthSource $preset)
    {
        $this->authSourcePreset = $preset;
        return $this;
    }

    /**
     * Returns the AuthSource preset.
     *
     * @return AuthSource|null
     */
    public function getAuthSourcePreset() : ?AuthSource
    {
        return $this->authSourcePreset;
    }
}

// vim: syntax=php sw=4 ts=4 et:
