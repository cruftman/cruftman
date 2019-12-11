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

use Cruftman\Ldap\Traits\HasAuthAttemptPreset;
use Cruftman\Ldap\Presets\AuthAttempt;
use Cruftman\Ldap\Presets\Connection;
use Cruftman\Ldap\Tools\Connector;
use Cruftman\Ldap\Tools\Binder;
use Cruftman\Ldap\Tools\Failover;

/**
 * Attempts to bind user using one or more connections (failover).
 */
class Attempt
{
    use HasAuthAttemptPreset;

    /**
     * @var callable
     */
    protected $connector = null;

    /**
     * @var callable
     */
    protected $binder = null;

    /**
     * @var Status
     */
    protected $status = null;

    /**
     * Initializes the object.
     *
     * @param  AuthAttempt $preset
     * @param  Status $status
     */
    public function __construct(
        AuthAttempt $preset,
        ?Status $status = null,
        ?Connector $connector = null,
        ?Binder $binder = null
    ) {
        $this->setAuthAttemptPreset($preset);
        $this->setStatus($status);
        $this->setConnector($connector);
        $this->setBinder($binder);
    }

    /**
     * Assigns *Status* object to *$this*.
     * @return Attempt $this
     */
    public function setStatus(?Status $status = null)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Returns the *Status* object assigned with *setStatus()*.
     * @return Status
     */
    public function getStatus() : Status
    {
        if ($this->status === null) {
            $this->setStatus(new Status);
        }
        return $this->status;
    }

    /**
     * Sets the function used to create Ldap instances.
     * @param Connector|null $connector
     * @return Attempt $this
     */
    public function setConnector(?Connector $connector)
    {
        $this->connector = $connector;
        return $this;
    }

    /**
     * Returns the ldap constructor callback used to create Ldap isntances.
     * @return Connector|null
     */
    public function getConnector() : Connector
    {
        if ($this->connector === null) {
            $this->setConnector(new Connector);
        }
        return $this->connector;
    }

    /**
     * Sets the function used to create Ldap instances.
     * @param Binder|null $binder
     * @return Attempt $this
     */
    public function setBinder(?Binder $binder)
    {
        $this->binder = $binder;
        return $this;
    }

    /**
     * Returns the ldap constructor callback used to create Ldap isntances.
     * @return Binder|null
     */
    public function getBinder() : Binder
    {
        if ($this->binder === null) {
            $this->setBinder(new Binder);
        }
        return $this->binder;
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

        $this->getStatus()->setBindResult($result)
                          ->setBindDn($bindDn)
                          ->setBindLdap($ldap)
                          ->setBindConnection($connection);

        return $result;
    }

    /**
     * Invoked when all connections failed.
     * @param  array $connections
     * @param  array $arguments
     */
    protected function bindFallback(array $connections, array $arguments) : bool
    {
        $this->getStatus()->resetBindStatus();
        return false;
    }
}

// vim: syntax=php sw=4 ts=4 et:
