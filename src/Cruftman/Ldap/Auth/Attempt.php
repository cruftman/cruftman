<?php
/**
 * @file src/Cruftman/Ldap/Auth/Attempt.php
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
use Korowai\Lib\Ldap\Adapter\AdapterInterface;

use Cruftman\Ldap\Presets\Search;
use Cruftman\Ldap\Traits\HasAuthAttemptPreset;
use Cruftman\Ldap\Traits\HasAuthStatus;
use Cruftman\Ldap\Traits\HasConnectorTool;
use Cruftman\Ldap\Traits\HasBinderTool;
use Cruftman\Ldap\Traits\HasFinderTool;
use Cruftman\Ldap\Presets\AuthAttempt;
use Cruftman\Ldap\Presets\Connection;
use Cruftman\Ldap\Tools\Failover;

/**
 * Attempts to bind user using one or more connections (failover).
 */
class Attempt
{
    use HasAuthAttemptPreset,
        HasAuthStatus,
        HasConnectorTool,
        HasBinderTool,
        HasFinderTool;

    /**
     * Initializes the object.
     *
     * @param  AuthAttempt $preset
     */
    public function __construct(AuthAttempt $preset)
    {
        $this->setAuthAttemptPreset($preset);
    }

    /**
     * Attempt to authenticate using LDAP bind method.
     *
     * @param  array $arguments
     * @param  Connection|null $connection
     *
     * @return bool
     * @throws LdapException
     */
    public function bind(array $arguments, ?Connection $connection = null) : bool
    {
        if ($connection !== null) {
            $connections = [$connection];
        } else {
            $connections = $this->getAuthAttemptPreset()->connections();
            if ($connections === null) {
                // FIXME: specialized exception?
                throw new \RuntimeException('Missing "connections" in AuthAttempt preset, check your config.');
            }
        }
        return $this->tryConnections($connections, $arguments);
    }

    /**
     * Tries to bind using an array of Connection presets (failover).
     *
     * @param  array $connections
     * @param  array $arguments
     *
     * @return bool
     * @throws LdapException
     */
    protected function tryConnections(array $connections, array $arguments) : bool
    {
        return (new Failover(
            function (Connection $connection) use ($arguments) {
                return $this->tryConnection($connection, $arguments);
            },
            function (array $connections) use ($arguments) {
                return $this->bindFallback($connections, $arguments);
            }
        ))->tryWith($connections);
    }

    /**
     * Tries to bind using a Connection preset.
     *
     * @param  Connection $connection
     * @param  array $arguments
     *
     * @return bool
     * @throws LdapException
     */
    protected function tryConnection(Connection $connection, array $arguments) : bool
    {
        $binding = $this->getAuthAttemptPreset()->binding();
        $bindDn = null;

        try {
            $ldap = $this->getConnector()->createLdap($connection, $arguments);
            $result = $this->getBinder()->bindDn($binding, $ldap, $arguments, $bindDn);
        } catch (LdapException $exception) {
            if ($exception->getCode() !== 0x31) {
                throw $exception;
            }
            // Invalid Credentials
            $result = false;
        }

        $this->getAuthStatus()->setBindResult($result)
                              ->setBindDn($bindDn)
                              ->setBindLdap($ldap)
                              ->setBindConnection($connection)
                              ->setBindEntry(null);

        if ($result) {
            $result = $this->postBindActions($arguments);
        }
        return $result;
    }

    /**
     * Performs all the necessary post-bind actions.
     *
     * @param  array $arguments
     * @return bool
     */
    protected function postBindActions(array $arguments) : bool
    {
        return $this->postBindSearchIfRequested($arguments);
    }

    /**
     * Performs extra post-bind LDAP search if it's requested by this Attempt's
     * preset.
     *
     * @param  array $arguments
     * @return bool
     */
    protected function postBindSearchIfRequested(array $arguments) : bool
    {
        $preset = $this->getAuthAttemptPreset();
        if (($search = $preset->getSearchIfRequested($arguments)) !== null) {
            return $this->postBindSearch($search, $arguments);
        }
        return true;
    }

    /**
     * Performs a post-bind search.
     *
     * @param  Search $search
     * @param  array $arguments
     * @return bool ``false`` if filtering is enabled and the search failed to return an entry
     */
    protected function postBindSearch(Search $search, array $arguments) : bool
    {
        $bindDn = $this->getAuthStatus()->getBindDn();
        $bindLdap = $this->getAuthStatus()->getBindLdap();
        $entry = $this->findBindEntry($bindDn, $bindLdap, $arguments);
        if ($entry !== null) {
            $this->getAuthStatus()->setBindEntry(new Entry($entry));
        }
        return ($entry !== null) || (!$this->getAuthAttemptPreset()->filtering($arguments));
    }

    /**
     * Makes an LDAP search to find the entry specified in *$bindDn* argument.
     *
     * @param  string $bindDn
     * @param  AdapterInterface $ldap
     * @param  array $arguments
     *
     * @return \Korowai\Lib\Ldap\Entry|null
     *         ``null`` is returned if the number of resultant entries is other
     *         than ``1``
     */
    protected function findBindEntry(string $bindDn, AdapterInterface $ldap, array $arguments)
    {
        $arguments = array_merge(['binddn' => $bindDn], $arguments);
        $search = $this->getAuthAttemptPreset()->search();
        $result = $this->getFinder()->search($search, $ldap, $arguments);
        $entries = $result->getEntries(false);
        return (count($entries) === 1) ? $entries[0] : null;
    }

    /**
     * Invoked when all connections failed with recoverable error.
     *
     * @param  array $connections
     * @param  array $arguments
     */
    protected function bindFallback(array $connections, array $arguments) : bool
    {
        $this->getAuthStatus()->resetBindStatus();
        return false;
    }
}

// vim: syntax=php sw=4 ts=4 et:
