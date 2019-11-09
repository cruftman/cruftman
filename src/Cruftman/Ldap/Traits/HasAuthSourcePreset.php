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
 * Add a protected attribute named *$authSource* and public accessors.
 */
trait HasAuthSourcePreset
{
    /**
     * @var \Cruftman\Ldap\Preset\AuthSource
     */
    protected $authSource;

    /**
     * Sets $authSource to the object.
     *
     * @param  AuthSource $authSource
     * @return object $this
     */
    public function setAuthSource(AuthSource $authSource)
    {
        $this->authSource = $authSource;
        return $this;
    }

    /**
     * Returns the $authSource.
     *
     * @return AuthSource|null
     */
    public function getAuthSource() : ?AuthSource
    {
        return $this->authSource;
    }
}

// vim: syntax=php sw=4 ts=4 et:
