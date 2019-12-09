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
    public function createUnbound(Connection $connection, array $arguments) : LdapInterface
    {
        $config = $connection->config($arguments);
        return Ldap::createWithConfig($config);
    }

    /**
     * Creates Ldap instance (bound).
     *
     * @param Session $session
     * @param array $arguments
     * @return LdapInterface
     */
    public function createBound(Connection $connection, Binding $binding, array $arguments) : LdapInterface
    {
        $ldap = $this->createUnbound($session->connection(), $arguments);
        $this->getBinder()->bind($binding, $ldap, $arguments);
        return $ldap;
    }

    /**
     * Creates Ldap instance (bound).
     *
     * @param Session $session
     * @param array $arguments
     * @return LdapInterface
     */
    public function createSession(Session $session, array $arguments) : LdapInterface
    {
        return $this->createBound($session->connection(), $session->binding(), $arguments);
    }
}

// vim: syntax=php sw=4 ts=4 et:
