<?php
/**
 * @file src/Cruftman/Ldap/LdapService.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\Ldap;

class LdapService implements LdapServiceInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var LdapInterface[]
     */
    private $sessions = [];

    /**
     * Initializes the service object.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->sessions = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionNames() : array
    {
        return array_keys($this->config['sessions']);
    }

    /**
     * {@inheritdoc}
     */
    public function getSession(string $name) : LdapInterface
    {
        if(!isset($this->sessions[$name])) {
            $this->sessions[$name] = $this->createSession($name);
        }
        return $this->sessions[$name];
    }

    /**
     * Creates and returns new instance of LdapInterface.
     *
     * @param string $name
     */
    protected function createSession(string $name) : LdapInterface
    {
        $sessionConfig = $this->config['sessions'][$name];
        $connectionName = $sessionConfig['connection'];
        $connectionConfig = $this->config['connections'][$connectionName];
        $session = Ldap::createWithConfig($connectionConfig);
        $session->bind(...($sessionConfig['bind'] ?? []));
        return $session;
    }

    /**
     * Returns the $config array provided to constructor.
     *
     * @return array
     */
    public function getConfig() : array
    {
        return $this->config;
    }
}

// vim: syntax=php sw=4 ts=4 et:
