<?php
/**
 * @file src/Cruftman/Ldap/Service.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\Ldap;

use Illuminate\Support\Arr;

/**
 * @todo Write documentation
 */
class Service
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @var array
     */
    protected $authSources = [];

    /**
     * Initializes the service object.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->instances = [];
    }

    /**
     * Returns the whole $config.
     *
     * @return array
     */
    public function getConfig() : array
    {
        return $this->config;
    }

    /**
     * Get an item from configuration array using "dot" notation.
     *
     * @param  string $key
     * @param  $default
     */
    public function getConfigItem(string $key, $default = null)
    {
        return Arr::get($this->getConfig(), $key, $default);
    }

    /**
     * Get an item from configuration array using "dot" notation.
     *
     * @param  string $key
     * @param  $default
     */
    public function getConfigItemOrFail(string $key)
    {
        if (($item = $this->getConfigItem($key)) === null) {
            // FIXME: specialized exception?
            throw new \Exception('configuration for "ldap.'.$key.'" does not exist');
        }
        return $item;
    }

    /**
     * Returns a list of available Ldap instance names.
     *
     * The returned array includes names of already created Ldap instances as
     * well as those defined in config but not yet created.
     *
     * @return array
     */
    public function getLdapInstanceNames() : array
    {
        return $this->getEntityNames($this->getConfigItem('instances', []), $this->instances);
    }

    /**
     * Returns a list of available search query names.
     *
     * @return array
     */
    public function getSearchQueryNames() : array
    {
        return $this->getEntityNames($this->getConfigItem('searches', []), $this->queries);
    }

    /**
     * Returns a list of available search query names.
     *
     * @return array
     */
    public function getAuthSourceNames() : array
    {
        return $this->getEntityNames($this->getConfigItem('auth.sources', []), $this->authSources);
    }

    /**
     * A helper method for other ``getXxxNames()`` methods (e.g. ``getLdapInstanceNames()``).
     *
     * @param  array $config part of the config which defines named entities,
     * @param  array $entities an array of already initialized entities/instances.
     * @return array
     */
    protected function getEntityNames(array $config, array $entities) : array
    {
        return array_unique(array_merge(array_keys($config), array_keys($entities)));
    }

    /**
     * Returns preconfigured instance of LdapInterface.
     *
     * @param string $name Instance name - one of the keys.
     *
     * @return \Korowai\Lib\Ldap\LdapInterface
     * @throws \OutOfBoundsException
     */
    public function getLdapInstance(string $name) : LdapInterface
    {
        if (($this->instances[$name] ?? null) === null) {
            $this->instances[$name] = $this->createLdapInstance($name);
        }
        return $this->instances[$name];
    }

    /**
     * Returns preconfigured LDAP search query.
     *
     * @param  string $name
     * @return SearchQueryTemplate
     * @throws \OutOfBoundsException
     */
    public function getSearchQuery(string $name) : SearchQueryTemplate
    {
        if (($this->queries[$name] ?? null) === null) {
            $this->queries[$name] = $this->createSearchQuery($name);
        }
        return $this->queries[$name];
    }

    /**
     * Returns preconfigured LDAP search query.
     *
     * @param  string $name
     * @return SearchQueryTemplate
     * @throws \OutOfBoundsException
     */
    public function getAuthSource(string $name) : AuthSource
    {
        if (($this->authSources[$name] ?? null) === null) {
            $this->authSources[$name] = $this->createAuthSource($name);
        }
        return $this->authSources[$name];
    }

    /**
     * Creates and returns an instance of LdapInterface
     *
     * @param  string $name
     * @return \Korowai\Lib\Ldap\LdapInterface
     * @throws \OutOfBoundsException
     */
    protected function createLdapInstance(string $name) : LdapInterface
    {
        $instancePath = 'instances.'.$name;
        $instanceConfig = $this->getConfigItemOrFail($instancePath);

        $connectionName = $this->getConfigItemOrFail($instancePath.'.connection');
        $connectionPath = 'connections.'.$connectionName;
        $connectionConfig = $this->getConfigItemOrFail($connectionPath);

        $ldap = Ldap::createWithConfig($connectionConfig);

        if (($bindingName = $this->getConfigItem($instancePath.'.bind')) !== null) {
            $bindingPath = 'bindings.'.$bindingName;
            $bindDn = $this->getConfigItemOrFail($bindingPath.'.0');
            $bindPw = $this->getConfigItemOrFail($bindingPath.'.1');
            $ldap->bind($bindDn, $bindPw);
        }
        return $ldap;
    }

    protected function createSearchQuery(string $name) : SearchQueryTemplate
    {
        $path = 'searches.'.$name;
        $config = $this->getConfigItemOrFail($path);
        return new SearchQueryTemplate($this, $config);
    }

    protected function createAuthSource(string $name) : AuthSource
    {
        $path = 'auth.sources.'.$name;
        $config = $this->getConfigItemOrFail($path);
        return new AuthSource($this, $config);
    }

//    /**
//     * Returns a configuration array for named instance.
//     *
//     * @param  string $name
//     * @return array
//     * @throws \OutOfBoundsException
//     */
//    protected function getInstanceConfig(string $name) : array
//    {
//        $instances = $this->getConfigItem('instances', []);
//        if (!array_key_exists($name, $instances)) {
//            // FIXME: specialized exception?
//            throw new \OutOfBoundsException('undefined LDAP instance: "'.$name.'"');
//        }
//        return $instances[$name];
//    }
//
//    /**
//     * Returns a configuration array for named connection.
//     *
//     * @param  string $name
//     * @return array
//     * @throws \OutOfBoundsException
//     */
//    protected function getConnectionConfig(string $name) : array
//    {
//        $connections = $this->getConfigItem('connections', []);
//        if (!array_key_exists($name, $connections)) {
//            // FIXME: specialized exception?
//            throw new \OutOfBoundsException('undefined LDAP connection: "'.$name.'"');
//        }
//        return $connections[$name];
//    }
//
//    /**
//     * Returns a configuration array for named connection.
//     *
//     * @param  string $name
//     * @return array
//     * @throws \OutOfBoundsException
//     */
//    protected function getBindingConfig(string $name) : array
//    {
//        $bindings = $this->getConfigItem('bindings', []);
//        if (!array_key_exists($name, $bindings)) {
//            // FIXME: specialized exception?
//            throw new \OutOfBoundsException('undefined LDAP binding: "'.$name.'"');
//        }
//        return $bindings[$name];
//    }
//
//    /**
//     * Returns a configuration array for named connection.
//     *
//     * @param  string $name
//     * @return array
//     * @throws \OutOfBoundsException
//     */
//    protected function getSearchConfig(string $name) : array
//    {
//        $bindings = $this->getConfigItem('searches', []);
//        if (!array_key_exists($name, $bindings)) {
//            // FIXME: specialized exception?
//            throw new \OutOfBoundsException('undefined LDAP search: "'.$name.'"');
//        }
//        return $bindings[$name];
//    }
}

// vim: syntax=php sw=4 ts=4 et:
