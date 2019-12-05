<?php
/**
 * @file src/Cruftman/Ldap/Auth/Status.php
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
use Cruftman\Ldap\Presets\Connection;

/**
 * Authentication status.
 *
 * Encapsulates authentication result and additional attributes recorded during
 * the authentication.
 */
class Status
{
    /**
     * @var bool
     */
    protected $bindResult;

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
        $keys = [
            'bindResult',
            'bindDn',
            'bindLdap',
            'bindConnection',
            'source'
        ];
        foreach ($keys as $key) {
            $this->{$key} = $options[$key] ?? null;
        }
    }

    /**
     * Set the bind result.
     *
     * @param  bool|null $result
     * @return Status $this
     */
    public function setBindResult(?bool $result) : Status
    {
        $this->bindResult = $result;
        return $this;
    }

    /**
     * Returns the bind result.
     *
     * @return bool|null
     */
    public function getBindResult() : ?bool
    {
        return $this->bindResult;
    }

    /**
     * Set the authenticated entry's bind DN.
     *
     * @param  string|null $dn
     * @return Status $this
     */
    public function setBindDn(?string $dn) : Status
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
     * @return Status $this
     */
    public function setBindConnection(?Connection $connection) : Status
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
     * @return Status $this
     */
    public function setBindLdap(?LdapInterface $ldap) : Status
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
     * @param  Source|null $source
     * @return Status $this
     */
    public function setSource(?Source $source) : Status
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Returns the Source object used for authentication.
     *
     * @return Source|null
     */
    public function getSource() : ?Source
    {
        return $this->source;
    }

    /**
     * Clear all the attributes describing bind operation status.
     *
     * @return Status
     */
    public function resetBindStatus() : Status
    {
        $this->setBindResult(null);
        $this->setBindDn(null);
        $this->setBindLdap(null);
        $this->setBindConnection(null);
        return $this;
    }
}

// vim: syntax=php sw=4 ts=4 et:
