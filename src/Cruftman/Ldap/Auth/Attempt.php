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
     * @todo Write documentation
     */
    protected function postBindActions(array $arguments)
    {
        return $this->postBindSearchIfRequested($arguments);
    }

    /**
     * @todo Write documentation
     */
    protected function postBindSearchIfRequested(array $arguments)
    {
        if (($search = $this->postBindSearchRequested($arguments)) !== null) {
            return $this->postBindSearch($search, $arguments);
        }
        return true;
    }

    /**
     * @todo Write documentation
     */
    protected function postBindSearchRequested(array $arguments) : ?Search
    {
        $preset = $this->getAuthAttemptPreset();
        $filtering = $preset->filtering($arguments) ?? true;
        $fetching = $preset->fetching($arguments) ?? true;
        return ($filtering || $fetching) ? $preset->search() : null;
    }

    /**
     * @todo Write documentation
     */
    protected function postBindSearch(Search $search, array $arguments)
    {
        $bindDn = $this->getAuthStatus()->getBindDn();
        $bindLdap = $this->getAuthStatus()->getBindLdap();
        $entry = $this->getBindEntry($bindDn, $bindLdap, $arguments);
        if ($entry !== null) {
            $this->getAuthStatus()->setBindEntry(new Entry($entry));
        }
        return ($entry !== null) || (!$this->getAuthAttemptPreset()->filtering($arguments));
    }

    /**
     * @todo Write documentation
     */
    public function getBindEntry(string $bindDn, AdapterInterface $ldap, array $arguments)
    {
        $arguments = array_merge(['binddn' => $bindDn], $arguments);
        $search = $this->getAuthAttemptPreset()->search();
        $result = $this->getFinder()->search($search, $ldap, $arguments);
        $entries = $result->getEntries(false);
        return (count($entries) === 1) ? $entries[0] : null;
    }

    /**
     * Invoked when all connections failed.
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
