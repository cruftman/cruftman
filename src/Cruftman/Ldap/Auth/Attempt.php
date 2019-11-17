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

use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\Exception\LdapException;

use Cruftman\Ldap\Traits\HasAuthAttemptPreset;
use Cruftman\Ldap\Preset\AuthAttempt;
use Cruftman\Ldap\Preset\Connection;

/**
 * Attempts to bind user using one or more connections (failover).
 */
class Attempt
{
    use HasAuthAttemptPreset;

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
     * @param  Status $status
     * @param  array $arguments
     * @param  Connection|null $connection
     *
     * @return bool
     * @throws LdapException
     */
    public function bind(Status $status, array $arguments, ?Connection $connection = null) : bool
    {
        if ($connection !== null) {
            $connections = [$connection];
        } else {
            $connections = $this->getAuthAttemptPreset()->getConnections();
        }
        return $this->tryConnections($status, $connections, $arguments);
    }

    /**
     * Tries to bind using Connection presets specified in $connections.
     *
     * @param  Status $status
     * @param  Connection[] $connections
     * @param  array $arguments
     *
     * @return bool
     * @throws LdapException
     */
    protected function tryConnections(Status $status, array $connections, array $arguments) : bool
    {
        foreach ($connections as $connection) {
            try {
                return $this->tryConnection($status, $connection, $arguments);
            } catch (LdapException $exception) {
                $this->rethrowIfUnrecoverable($exception);
            }
        }
        $status->resetBindStatus();
        return false;
    }

    /**
     * Tries to bind using the Connection preset specified in $connection.
     *
     * @param  Status $status
     * @param  Connection $connection
     * @param  array $arguments
     *
     * @return bool
     * @throws LdapException
     */
    protected function tryConnection(Status $status, Connection $connection, array $arguments) : bool
    {
        $binding = $this->getAuthAttemptPreset()->getBinding();

        try {
            $ldap = $connection->createLdap($arguments);
            $result = $binding->bindLdapInterface($ldap, $arguments);
        } catch (LdapException $exception) {
            if ($exception->getCode() !== 0x31) {
                throw $exception;
            }
            // Invalid Credentials
            $result = false;
        }

        $status->setBindResult($result)
               ->setBindDn($binding->getBindDn($arguments))
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
