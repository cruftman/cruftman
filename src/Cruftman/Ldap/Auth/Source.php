<?php
/**
 * @file src/Cruftman/Ldap/Auth/Source.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Auth;

use Korowai\Lib\Ldap\Ldap;
use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\Exception\LdapException;

use Cruftman\Ldap\Traits\HasAuthSourcePreset;

use Cruftman\Ldap\Presets\AuthSource;
use Cruftman\Ldap\Presets\Search;
use Cruftman\Ldap\Presets\Session;
use Cruftman\Ldap\Presets\Connection;
use Cruftman\Ldap\Tools\Failover;
use Cruftman\Ldap\Tools\Connector;
use Cruftman\Ldap\Tools\Finder;

/**
 * Authentication source.
 */
class Source
{
    use HasAuthSourcePreset;

    /**
     * @var Attempt
     */
    protected $attempt = null;

    /**
     * @var Connector
     */
    protected $connector = null;

    /**
     * Initializes the object.
     *
     * @param  AuthSource $preset
     */
    //public function __construct(AuthSource $preset, ?Attempt $attempt = null, ?callable $connector = null)
    public function __construct(AuthSource $preset, ?Attempt $attempt = null, ?Connector $connector = null)
    {
        $this->setAuthSourcePreset($preset);
        $this->setAttempt($attempt);
        $this->setConnector($connector);
    }

    /**
     * Sets the function used to create Ldap instances.
     * @param  Connector $connector
     * @return Source $this
     */
    public function setConnector(?Connector $connector = null)
    {
        $this->connector = $connector;
        return $this;
    }

    /**
     * Returns the ldap constructor callback used to create Ldap isntances.
     * @return Connector
     */
    public function getConnector() : Connector
    {
        if ($this->connector === null) {
            $this->setConnector(new Connector);
        }
        return $this->connector;
    }

    /**
     * @todo Write documentation.
     * @param  Attempt|null $attempt
     * @return Source $this
     */
    public function setAttempt(?Attempt $attempt)
    {
        $this->attempt = $attempt;
        return $this;
    }

    /**
     * Returns an Attempt object.
     *
     * @return Attempt|null
     */
    public function getAttempt() : Attempt
    {
        if ($this->attempt === null) {
            $attemptPreset = $this->getAuthSourcePreset()->attempt();
            $this->setAttempt(new Attempt($attemptPreset));
        }
        return $this->attempt;
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $arguments
     * @return Entry[]
     */
    public function search(array $arguments = []) : array
    {
        $search = $this->getAuthSourcePreset()->search();
        return $this->searchWithPreset($search, $arguments);
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $arguments
     * @return Entry[]
     */
    public function locate(array $arguments = []) : array
    {
        $search = $this->getAuthSourcePreset()->locate();
        return $this->searchWithPreset($search, $arguments);
    }

    /**
     * Search using a Search preset.
     *
     * @param  Search|null $search
     * @param  array $arguments
     * @return array
     */
    protected function searchWithPreset(?Search $search, array $arguments) : array
    {
        if ($search === null) {
            return [];
        }
        $sessions = $this->getAuthSourcePreset()->sessions();
        return $this->searchWithSessions($search, $sessions, $arguments);
    }

    /**
     * Search using multiple Session presets (failover).
     *
     * @param  Search $search
     * @param  Session[] $sessions
     * @param  array $arguments
     * @return Entry[]
     */
    protected function searchWithSessions(Search $search, array $sessions, array $arguments) : array
    {
        return (new Failover(
            function (Session $session) use ($search, $arguments) {
                return $this->searchWithSession($search, $session, $arguments);
            },
            function (array $sessions) use ($search, $arguments) {
                return $this->searchFallback($search, $sessions, $arguments);
            }
        ))->tryWith($sessions);
    }

    /**
     * Search using single Session preset.
     *
     * @param  Search $search
     * @param  Session $session
     * @param  array $arguments
     * @return Entry[]
     */
    protected function searchWithSession(Search $search, Session $session, array $arguments) : array
    {
        $connection = $session->connection();
        $binding = $session->binding();
        $ldap = $this->getConnector()->createAndBindLdap($connection, $binding, $arguments);
        $entries = $this->searchWithLdap($search, $ldap, $arguments);
        return $this->wrapEntries($entries, $connection);
    }

    /**
     * Perform search query using LdapInterface.
     *
     * @param  Search $search
     * @param  LdapInterface $ldap
     * @param  array $arguments
     * @return array
     */
    protected function searchWithLdap(Search $search, LdapInterface $ldap, array $arguments) : array
    {
//        $base = $search->base($arguments);
//        $filter = $search->filter($arguments);
//        $options = $search->options($arguments);
//        $result = $ldap->search($base, $filter, $options);
        $result = (new Finder)->search($search, $ldap, $arguments);
        return $result->getEntries(false);
    }

    /**
     * @param  Search $search
     * @param  array $sessions
     * @param  array $arguments
     * @return array
     */
    protected function searchFallback(Search $search, array $sessions, array $arguments) : array
    {
        // FIXME: notify that the whole failover algorithm failed.
        return [];
    }

    /**
     * @todo Write documentation
     * @param  array $entries
     * @param  Session $session
     * @return array
     */
    protected function wrapEntries(array $entries, Connection $connection) : array
    {
        return array_map(function ($entry) use ($connection) {
            return (new Entry($entry))->setConnectionPreset($connection)
                                      ->setSource($this);
        }, $entries);
    }
}

// vim: syntax=php sw=4 ts=4 et:
