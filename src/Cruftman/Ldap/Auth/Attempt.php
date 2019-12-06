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

use Korowai\Lib\Ldap\Ldap;
use Korowai\Lib\Ldap\Exception\LdapException;

use Cruftman\Ldap\Traits\HasAuthAttemptPreset;
use Cruftman\Ldap\Presets\AuthAttempt;
use Cruftman\Ldap\Presets\Connection;

/**
 * Attempts to bind user using one or more connections (failover).
 */
class Attempt
{
    use HasAuthAttemptPreset;

    /**
     * @var callable
     */
    protected $ldapConstructor;

    /**
     * @var Status
     */
    protected $status;

    /**
     * Initializes the object.
     *
     * @param  AuthAttempt $preset
     * @param  Status $status
     */
    public function __construct(AuthAttempt $preset, ?Status $status = null, ?callable $ldapConstructor = null)
    {
        $this->setAuthAttemptPreset($preset);
        $this->setStatus($status);
        $this->setLdapConstructor($ldapConstructor);
    }

    /**
     * Assigns *Status* object to *$this*.
     * @return Attempt $this
     */
    public function setStatus(?Status $status = null)
    {
        if ($status === null) {
            $this->status = new Status();
        } else {
            $this->status = $status;
        }
        return $this;
    }

    /**
     * Returns the *Status* object assigned with *setStatus()*.
     * @return Status
     */
    public function getStatus() : Status
    {
        return $this->status;
    }

    /**
     * Sets the function used to create Ldap instances.
     * @param callable $ldapConstructor
     * @return Attempt $this
     */
    public function setLdapConstructor(?callable $ldapConstructor = null)
    {
        if ($ldapConstructor === null) {
            $this->ldapConstructor = [Ldap::class, 'createWithConfig'];
        } else {
            $this->ldapConstructor = $ldapConstructor;
        }
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
     * Tries to bind using Connection presets specified in *$connections*.
     *
     * @param  array $connections
     * @param  array $arguments
     *
     * @return bool
     * @throws LdapException
     */
    protected function tryConnections(array $connections, array $arguments) : bool
    {
        foreach ($connections as $connection) {
            try {
                return $this->tryConnection($connection, $arguments);
            } catch (LdapException $exception) {
                $this->rethrowIfUnrecoverable($exception);
            }
        }
        $this->status->resetBindStatus();
        return false;
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
        $bindDn = $binding->dn($arguments);
        $bindPw = $binding->password($arguments);

        try {
            $config = $connection->ldapConfig($arguments);
            $ldap = call_user_func($this->ldapConstructor, $config);
            $result = $ldap->bind($bindDn, $bindPw);
        } catch (LdapException $exception) {
            if ($exception->getCode() !== 0x31) {
                throw $exception;
            }
            // Invalid Credentials
            $result = false;
        }

        $this->status->setBindResult($result)
                     ->setBindDn($bindDn)
                     ->setBindLdap($ldap)
                     ->setBindConnection($connection);

        return $result;
    }

    /**
     * Rethrow the $exception if can't be recovered with failover.
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
