<?php
/**
 * @file src/Cruftman/Ldap/Tools/Connector.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Tools;

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

    /**
     * @var callable
     */
    protected $constructor = null;

    /**
     * Initializes the object.
     *
     * @param  Binder|null $binder
     * @param  callable|null $constructor
     */
    public function __construct(?Binder $binder = null, ?callable $constructor = null)
    {
        if ($binder !== null) {
            $this->setBinder($binder);
        }
        if ($constructor !== null) {
            $this->setConstructor($constructor);
        }
    }

    /**
     * Assigns *Binder* functor to this object.
     * @param  Binder|null $binder
     * @return Connector $this
     */
    public function setBinder(?Binder $binder)
    {
        $this->binder = $binder;
        return $this;
    }

    /**
     * Returns the *Binder* functor assigned to this object.
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
     * Assigns a function that creates LdapInterface instances.
     *
     * @param  callable|null $constructor
     * @return Connector $this
     */
    public function setConstructor(?callable $constructor)
    {
        $this->constructor = $constructor;
        return $this;
    }

    /**
     * Returns the LdapInterface constructor assigned to this object.
     *
     * @return callable
     */
    public function getConstructor()
    {
        if ($this->constructor === null) {
            $this->setConstructor([Ldap::class, 'createWithConfig']);
        }
        return $this->constructor;
    }

    /**
     * Creates Ldap instance (unbound).
     *
     * @param  Connection $connection
     * @param  array $arguments
     * @return LdapInterface
     */
    public function createLdap(Connection $connection, array $arguments) : LdapInterface
    {
        $config = $connection->config($arguments);
        return call_user_func($this->getConstructor(), $config);
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
        $ldap = $this->createLdap($connection, $arguments);
        $this->getBinder()->bind($binding, $ldap, $arguments);
        return $ldap;
    }

    /**
     * Creates and binds Ldap using Session preset.
     *
     * @param  Session $session
     * @param  array $arguments
     * @return LdapInterface
     */
    public function createLdapWithSession(Session $session, array $arguments) : LdapInterface
    {
        return $this->createAndBindLdap($session->connection(), $session->binding(), $arguments);
    }
}

// vim: syntax=php sw=4 ts=4 et:
