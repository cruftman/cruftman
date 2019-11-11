<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasConnectionPreset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Preset\Connection;

/**
 * Add a protected attribute named *$connectionPreset* and public accessors.
 */
trait HasConnectionPreset
{
    /**
     * @var \Cruftman\Ldap\Preset\Connection
     */
    protected $connectionPreset;

    /**
     * Sets Connection preset to the object.
     *
     * @param  Connection $preset
     * @return object $this
     */
    public function setConnectionPreset(Connection $preset)
    {
        $this->connectionPreset = $preset;
        return $this;
    }

    /**
     * Returns the Connection preset.
     *
     * @return Connection|null
     */
    public function getConnectionPreset() : ?Connection
    {
        return $this->connectionPreset;
    }
}

// vim: syntax=php sw=4 ts=4 et:
