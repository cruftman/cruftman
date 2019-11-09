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
 * Add a protected attribute named *$connection* and public accessors.
 */
trait HasConnectionPreset
{
    /**
     * @var \Cruftman\Ldap\Preset\Connection
     */
    protected $connection;

    /**
     * Sets $connection to the object.
     *
     * @param  Connection $connection
     * @return object $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Returns the $connection.
     *
     * @return Connection|null
     */
    public function getConnection() : ?Connection
    {
        return $this->connection;
    }
}

// vim: syntax=php sw=4 ts=4 et:
