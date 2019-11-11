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

use Korowai\Lib\Ldap\Exception\LdapException;

use Cruftman\Ldap\Traits\HasAuthSourcePreset;

use Cruftman\Ldap\Preset\AuthSource;
use Cruftman\Ldap\Preset\Search;
use Cruftman\Ldap\Preset\Session;

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
     * Initializes the object.
     *
     * @param  AuthSource $preset
     */
    public function __construct(AuthSource $preset)
    {
        $this->setAuthSourcePreset($preset);
    }

    /**
     * Returns an Attempt object.
     *
     * @return Attempt
     */
    public function getAttempt() : Attempt
    {
        if (!isset($this->attempt)) {
            $this->attempt = new Attempt($this->getAuthSourcePreset()->getAuthAttempt());
        }
        return $this->attempt;
    }

    /**
     * Returns the nested Search preset.
     * @return Search|null
     */
    public function getSearchPreset() : ?Search
    {
        return $this->getAuthSourcePreset()->getSearch();
    }

    /**
     * Returns the nested Search preset.
     * @return Search|null
     */
    public function getLocatePreset() : ?Search
    {
        return $this->getAuthSourcePreset()->getLocate();
    }

    /**
     * Returns the nested array of Session presets.
     * @return Session[]|null
     */
    public function getSessionsPresets() : ?array
    {
        return $this->getAuthSourcePreset()->getSessions();
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $arguments
     * @return Entry[]
     */
    public function search(array $arguments = []) : array
    {
        $search = $this->getSearchPreset();
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
        $search = $this->getLocatePreset();
        return $this->searchWithPreset($search, $arguments);
    }

    /**
     * Search using a Search preset.
     *
     * @param  Search|null $search
     * @param  array $arguments
     * @return array
     */
    protected function searchWithPreset(?Search $search, array $arguments = []) : array
    {
        if ($search === null) {
            return [];
        }
        $sessions = $this->getAuthSourcePreset()->getSessions();
        return $this->searchWithSessions($search, $sessions, $arguments);
    }

    /**
     * Search using multiple Session presets (failover).
     *
     * @param  Search $search
     * @param  Session[] $session
     * @param  array $arguments
     * @return Entry[]
     */
    protected function searchWithSessions(Search $search, array $sessions, array $arguments = []) : array
    {
        foreach ($sessions as $session) {
            try {
                return $this->searchWithSession($search, $session, $arguments);
            } catch (LdapException $exception) {
                $this->rethrowIfUnrecoverable($exception);
            }
        }
        return [];
    }

    /**
     * Search using single Session preset.
     *
     * @param  Search $search
     * @param  Session $session
     * @param  array $arguments
     * @return Entry[]
     */
    protected function searchWithSession(Search $search, Session $session, array $arguments = []) : array
    {
        $entries = $this->doSearchWithSession($search, $session, $arguments);
        $connection = $session->getConnection();
        return array_map(function ($entry) use ($connection) {
            return (new Entry($entry))->setConnectionPreset($connection)
                                      ->setSource($this);
        }, $entries);
    }

    /**
     * Perform an actual search query.
     *
     * @param  Search $search
     * @param  Session $session
     * @param  array $arguments
     * @return Korowai\Lib\Ldap\Entry[]
     */
    protected function doSearchWithSession(Search $search, Session $session, array $arguments = []) : array
    {
        $ldap = $session->createLdap($arguments);
        $query = $search->createQuery($ldap, $arguments);
        $result = $query->getResult();
        return $result->getEntries(false);
    }

    /**
     * Rethrow the $exception if it can't be recovered with failover.
     *
     * @param  LdapException $exception
     * @throws LdapException
     */
    protected function rethrowIfUnrecoverable(LdapException $exception)
    {
        if ($exception->getCode() !== -1) {
            throw $exception;
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
