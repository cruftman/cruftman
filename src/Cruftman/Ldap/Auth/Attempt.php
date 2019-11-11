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
use Korowai\Lib\Ldap\Entry as LdapEntry;

use Cruftman\Ldap\Traits\HasAuthAttemptPreset;
use Cruftman\Ldap\Preset\AuthAttempt;
use Cruftman\Ldap\Preset\Connection;

/**
 * @todo Write documentation.
 */
class Attempt
{
    use HasAuthAttemptPreset;

    /**
     * @var AttemptState
     */
    protected $state;

    /**
     * Initializes the object.
     *
     * @param  AuthAttempt $preset
     */
    public function __construct(AuthAttempt $preset)
    {
        $this->setAuthAttemptPreset($preset);
        $this->state = null;
    }

    /**
     * Returns the AttemptState object.
     *
     * @return AttemptState|null
     */
    public function getState() : ?AttemptState
    {
        return $this->state;
    }

    /**
     * Attempt to authenticate with the bind method.
     *
     * @param  array $arguments
     * @param  Connection|null $connection
     * @return bool
     */
    public function bind(array $arguments = [], ?Connection $connection = null) : bool
    {
        $this->state = null;
        if ($connection === null) {
            $connections = $this->getAuthAttemptPreset()->getConnections();
            return $this->tryConnections($connections, $arguments);
        } else {
            return $this->tryConnection($connection, $arguments);
        }
    }

    /**
     * @todo Write documentation.
     * @param  Connection[] $connections
     * @param  array $arguments
     * @return bool
     * @throws LdapException
     */
    protected function tryConnections(array $connections, array $arguments = []) : bool
    {
        $binding = $this->getAuthAttemptPreset()->getBinding();
        foreach ($connections as $connection) {
            try {
                return $this->tryConnection($connection, $arguments);
            } catch (LdapException $exception) {
                $this->rethrowIfUnrecoverable($exception);
            }
        }
        return false;
    }

    /**
     * Tries to bind using the Binding preset and the Connection preset specified.
     *
     * @param  Connection $connection
     * @param  array $arguments
     * @return bool
     */
    protected function tryConnection(Connection $connection, array $arguments = []) : bool
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

        if ($result) {
            $this->state = new AttemptState([
                'bindDn' => $binding->getBindDn($arguments),
                'bindLdap' => $ldap,
                'bindConnection' => $connection,
            ]);
        }
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
