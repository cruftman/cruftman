<?php
/**
 * @file src/Cruftman/Ldap/Auth/SearchEntry.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Auth;

use Korowai\Lib\Ldap\EntryInterface;
use Cruftman\Ldap\Preset\Connection;
use Cruftman\Ldap\Preset\AuthSource;

/**
 * @todo Write documentation.
 */
class SearchEntry
{
    /**
     * @var EntryInterface
     */
    protected $entry;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var AuthSource
     */
    protected $source = null;

    /**
     * Initializes the object.
     *
     * @param  EntryInterface $entry
     * @param  Connection $connection
     */
    public function __construct(EntryInterface $entry, Connection $connection)
    {
        $this->entry = $entry;
        $this->connection = $connection;
    }

    /**
     * @todo Write documentation.
     *
     * @return EntryInterface
     */
    public function getEntry() : EntryInterface
    {
        return $this->entry;
    }

    /**
     * @todo Write documentation.
     *
     * @return Connection
     */
    public function getConnection() : Connection
    {
        return $this->connection;
    }

    /**
     * @todo Write documentation.
     * @param  AuthSource $source
     * @return object $this
     */
    public function setAuthSource(AuthSource $source) : SearchEntry
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @todo Write documentation.
     * @return AuthSource|null
     */
    public function getAuthSource() : ?AuthSource
    {
        return $this->source;
    }

    /**
     * @todo Write documentation.
     * @return string
     */
    public function getDn() : string
    {
        return $this->getEntry()->getDn();
    }

    /**
     * @todo Write documentation.
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->getEntry()->getAttributes();
    }

    /**
     * @todo Write documentation.
     *
     * @param  string $name
     * @return array
     */
    public function getAttribute(string $name) : array
    {
        return $this->getEntry()->getAttribute($name);
    }
}

// vim: syntax=php sw=4 ts=4 et:
