<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasAuthAttemptPreset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Presets\AuthAttempt;

/**
 * Add a protected attribute named *$authAttemptPreset* and public accessors.
 */
trait HasAuthAttemptPreset
{
    /**
     * @var AuthAttempt
     */
    protected $authAttemptPreset;

    /**
     * Sets AuthAttempt preset to the object.
     *
     * @param  AuthAttempt $preset
     * @return object $this
     */
    public function setAuthAttemptPreset(AuthAttempt $preset)
    {
        $this->authAttemptPreset = $preset;
        return $this;
    }

    /**
     * Returns the AuthAttempt preset.
     *
     * @return AuthAttempt|null
     */
    public function getAuthAttemptPreset() : ?AuthAttempt
    {
        return $this->authAttemptPreset;
    }
}

// vim: syntax=php sw=4 ts=4 et:
