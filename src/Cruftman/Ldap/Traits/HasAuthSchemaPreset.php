<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasAuthSchemaPreset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Preset\AuthSchema;

/**
 * Add a protected attribute named *$authSchemaPreset* and public accessors.
 */
trait HasAuthSchemaPreset
{
    /**
     * @var AuthSchema
     */
    protected $authSchemaPreset;

    /**
     * Sets AuthSchema $preset to the object.
     *
     * @param  AuthSchema $preset
     * @return object $this
     */
    public function setAuthSchemaPreset(AuthSchema $preset)
    {
        $this->authSchemaPreset = $preset;
        return $this;
    }

    /**
     * Returns the AuthSchema preset.
     *
     * @return AuthSchema|null
     */
    public function getAuthSchemaPreset() : ?AuthSchema
    {
        return $this->authSchemaPreset;
    }
}

// vim: syntax=php sw=4 ts=4 et:
