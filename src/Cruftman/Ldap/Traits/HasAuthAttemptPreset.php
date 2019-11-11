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

use Cruftman\Ldap\Preset\AuthAttempt;

/**
 * Add a protected attribute named *$authAttempt* and public accessors.
 */
trait HasAuthAttemptPreset
{
    /**
     * @var \Cruftman\Ldap\Preset\AuthAttempt
     */
    protected $authAttempt;

    /**
     * Sets $authAttempt to the object.
     *
     * @param  AuthAttempt $authAttempt
     * @return object $this
     */
    public function setAuthAttempt(AuthAttempt $authAttempt)
    {
        $this->authAttempt = $authAttempt;
        return $this;
    }

    /**
     * Returns the $authAttempt.
     *
     * @return AuthAttempt|null
     */
    public function getAuthAttempt() : ?AuthAttempt
    {
        return $this->authAttempt;
    }
}

// vim: syntax=php sw=4 ts=4 et:
