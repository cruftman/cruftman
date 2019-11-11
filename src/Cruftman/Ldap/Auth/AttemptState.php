<?php
/**
 * @file src/Cruftman/Ldap/Auth/AttemptState.php
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
use Cruftman\Ldap\Preset\Connection;
use Cruftman\Ldap\Preset\AuthSource;

/**
 * @todo Write documentation.
 */
class AttemptState
{
    /**
     * @var string
     */
    protected $bindDn;

    /**
     * @var LdapInterface
     */
    protected $bindLdap;

    /**
     * @var Connection
     */
    protected $bindConnection;

    /**
     * @var Source
     */
    protected $source;

    /**
     * Initializes the object.
     *
     * @param  array $options
     */
    public function __construct(array $options = [])
    {
        $this->initFromArray($options);
    }

    /**
     * Initialize the object.
     */
    protected function initFromArray(array $options = [])
    {
        $this->bindDn = $options['bindDn'] ?? null;
        $this->bindLdap = $options['bindLdap'] ?? null;
        $this->bindConnection = $options['bindConnection'] ?? null;
        $this->source = $options['source'] ?? null;
    }

    /**
     * Set the authenticated entry's bind DN.
     *
     * @param  string|null $dn
     * @return AttemptState $this
     */
    public function setBindDn(?string $dn) : AttemptState
    {
        $this->bindDn = $dn;
        return $this;
    }

    /**
     * Returns the authenticated entry's bind DN.
     *
     * @return string|null
     */
    public function getBindDn() : ?string
    {
        return $this->bindDn;
    }

    /**
     * Set the Connection preset used for authentication.
     *
     * @param  Connection|null $connection
     * @return AttemptState $this
     */
    public function setBindConnection(?Connection $connection) : AttemptState
    {
        $this->bindConnection = $connection;
        return $this;
    }

    /**
     * Returns the Connection preset used for authentication.
     *
     * @return Connection|null
     */
    public function getBindConnection() : ?Connection
    {
        return $this->bindConnection;
    }

    /**
     * Set the instance of LdapInterface used for authentication.
     *
     * @param  LdapInterface|null $ldap
     * @return AttemptState $this
     */
    public function setBindLdap(?LdapInterface $ldap) : AttemptState
    {
        $this->bindLdap = $ldap;
        return $this;
    }

    /**
     * Returns the instance of LdapInterface used for authentication.
     *
     * @return LdapInterface
     */
    public function getBindLdap() : ?LdapInterface
    {
        return $this->bindLdap;
    }

    /**
     * Sets the Source object used for authentication.
     *
     * @param  Source|null $authSource
     * @return AttemptState $this
     */
    public function setSource(?Source $source) : AttemptState
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Returns the AuthSource preset used for authentication.
     *
     * @return Source|null
     */
    public function getSource() : ?Source
    {
        return $this->source;
    }
}

// vim: syntax=php sw=4 ts=4 et:
