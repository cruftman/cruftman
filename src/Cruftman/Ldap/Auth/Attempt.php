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
     * Initializes the object.
     *
     * @param  AuthAttempt $attempt
     */
    public function __construct(AuthAttempt $attempt)
    {
        $this->setAuthAttempt($attempt);
    }

    /**
     * @todo Write documentation.
     * @param  Entry $entry
     * @param  array $arguments
     * @return bool
     */
    public function bindEntry(Entry $entry, array $arguments = []) : bool
    {
        if (($connection = $entry->getConnection()) !== null) {
            $arguments = array_merge(['dn' => $entry->getDn()], $arguments);
            $result = $this->tryConnection($connection, $arguments, $ldap);
        } elseif(($connections = $this->getAuthAttempt()->getConnections()) !== null) {
            $result = $this->tryConnections($connections, $arguments, $connection, $ldap);
            $entry->setConnection($connection);
        } else {
            // FIXME: specialized exception...
            throw new \Exception('Could not determine connection presets for bind attempt.');
        }

        $entry->setAuthResult($result)
              ->setUserLdap($ldap);

        return $result;
    }

    /**
     * @todo Write documentation.
     * @param  Entry $entry
     * @param  array $arguments
     */
    public function bindDirect(Entry &$entry = null, array $arguments = []) : bool
    {
        $connections = $this->getAuthAttempt()->getConnections();
        if (($result = $this->tryConnections($connections, $arguments, $connection, $ldap)) !== true) {
            return false;
        }
        if (($entry = $this->retrieveBindEntry($ldap, $arguments)) === null) {
            return false;
        }
        $entry->setConnection($connection)
              ->setAuthResult($result)
              ->setUserLdap($ldap);
        return true;
    }

    /**
     * @todo Write documentation
     * @param  LdapInterface $ldap
     * @param  array $arguments
     */
    protected function retrieveBindEntry(LdapInterface $ldap, array $arguments = []) : ?Entry
    {
        $preset = $this->getAuthAttempt();
        $dn = $preset->substOptionOrFail('bind.0', $arguments);
        $filter = $preset->substOption('filter', $arguments, 'objectclass=*');
        $attributes = $preset->substOption('attributes', $arguments, ['*']);
        $options = ['scope' => 'base', 'attributes' => $attributes];
        $entries = $ldap->search($dn, $filter, $options)->getEntries(false);
        if (count($entries) === 1) {
            return new Entry($entries[0]);
        } else {
            return null;
        }
    }

    /**
     * @todo Write documentation.
     * @param  Connection[] $connections
     * @param  array $arguments
     * @param  Connection $connection
     * @param  LdapInterface $ldap
     * @return bool
     */
    protected function tryConnections(
        array $connections,
        array $arguments = [],
        Connection &$connection = null,
        LdapInterface &$ldap = null
    ) : bool {
        foreach ($connections as $connection) {
            try {
                return $this->tryConnection($connection, $arguments, $ldap);
            } catch (LdapException $exception) {
                if ($exception->getCode() !== -1) {
                    throw $exception;
                }
            }
        }
        $connection = null;
        return false;
    }

    /**
     * @todo Write documentation.
     * @param  Connection $connection
     * @param  array $arguments
     * @return bool
     */
    protected function tryConnection(Connection $connection, array $arguments = [], LdapInterface &$ldap = null) : bool
    {
        try {
            $ldap = $connection->createLdap($arguments);
            return $this->getAuthAttempt()->getBinding()->bindLdapInterface($ldap, $arguments);
        } catch (LdapException $exception) {
            if ($exception->getCode() === 0x31) {
                // Invalid Credentials
                return false;
            }
            throw $exception;
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
