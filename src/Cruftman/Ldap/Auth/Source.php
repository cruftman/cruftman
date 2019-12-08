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
     * @var callable
     */
    protected $ldapConstructor = null;

    /**
     * Initializes the object.
     *
     * @param  AuthSource $preset
     */
    public function __construct(AuthSource $preset, ?Attempt $attempt = null, ?callable $ldapConstructor = null)
    {
        $this->setAuthSourcePreset($preset);
        $this->setAttempt($attempt);
        $this->setLdapConstructor($ldapConstructor);
    }

    /**
     * Sets the function used to create Ldap instances.
     * @param callable $ldapConstructor
     * @return Attempt $this
     */
    public function setLdapConstructor(?callable $ldapConstructor = null)
    {
        $this->ldapConstructor = $ldapConstructor ?? [Ldap::class, 'createWithConfig'];
        return $this;
    }

    /**
     * Returns the ldap constructor callback used to create Ldap isntances.
     * @return callable
     */
    public function getLdapConstructor()
    {
        return $this->ldapConstructor;
    }

    /**
     * @todo Write documentation.
     * @param  Attempt|null $attempt
     * @return Source $this
     */
    public function setAttempt(?Attempt $attempt)
    {
        $this->attempt = $attempt ?? new Attempt($this->getAuthSourcePreset()->attempt());
        return $this;
    }

    /**
     * Returns an Attempt object.
     *
     * @return Attempt|null
     */
    public function getAttempt() : ?Attempt
    {
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
        $config = $connection->config($arguments);
        $ldap = call_user_func($this->getLdapConstructor(), $config);
        $ldap->bind($binding->dn($arguments), $binding->password($arguments));
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
        $base = $search->base($arguments);
        $filter = $search->filter($arguments);
        $options = $search->options($arguments);
        $result = $ldap->search($base, $filter, $options);
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
