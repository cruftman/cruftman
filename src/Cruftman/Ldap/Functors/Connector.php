<?php
/**
 * @file src/Cruftman/Ldap/Functors/Connector.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Functors;

use Cruftman\Ldap\Presets\Connection;
use Cruftman\Ldap\Presets\Session;
use Cruftman\Ldap\Presets\Binding;
use Korowai\Lib\Ldap\Ldap;
use Korowai\Lib\Ldap\LdapInterface;

/**
 * Creates instances of LdapInterface.
 */
class Connector
{
    /**
     * @var Binder
     */
    protected $binder = null;

    public function __construct(Binder $binder = null)
    {
        $this->setBinder($binder);
    }

    /**
     * Assigns *Binder* functor to this object.
     * @param  Binder|null $binder
     * @return Connector $this
     */
    public function setBinder(?Binder $binder)
    {
        if ($binder === null) {
            $binder = new Binder;
        }
        $this->binder = $binder;
        return $this;
    }

    /**
     * Returns the *Binder* functor assigned to this object.
     * @return Binder|null
     */
    public function getBinder() : ?Binder
    {
        return $this->binder;
    }


    /**
     * Creates Ldap instance (unbound).
     *
     * @param Connection $connection
     * @param array $arguments
     * @return LdapInterface
     */
    public function createLdap(Connection $connection, array $arguments) : LdapInterface
    {
        $config = $connection->config($arguments);
        return Ldap::createWithConfig($config);
    }

    /**
     * Creates and binds new Ldap instance using Connection and Binding presets.
     *
     * @param  Connection $connection
     * @param  Binding $binding
     * @param  array $arguments
     * @return LdapInterface
     */
    public function createAndBindLdap(Connection $connection, Binding $binding, array $arguments) : LdapInterface
    {
        $ldap = $this->createLdap($session->connection(), $arguments);
        $this->getBinder()->bind($binding, $ldap, $arguments);
        return $ldap;
    }

    /**
     * Creates and binds Ldap using Session preset.
     *
     * @param Session $session
     * @param array $arguments
     * @return LdapInterface
     */
    public function createLdapWithSession(Session $session, array $arguments) : LdapInterface
    {
        return $this->createAndBindLdap($session->connection(), $session->binding(), $arguments);
    }
}

// vim: syntax=php sw=4 ts=4 et:
