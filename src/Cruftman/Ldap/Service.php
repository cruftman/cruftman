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
use Symfony\Component\OptionsResolver\OptionsResolver;

use Cruftman\Support\Traits\HasOptions;
use Cruftman\Support\Traits\ValidatesOptions;

/**
 * @todo Write documentation
 */
class Service
{
    use HasOptions,
        ValidatesOptions;

    /**
     * @var array
     */
    protected $connections = [];

    /**
     * @var array
     */
    protected $bindings = [];

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
     * @param array $confi$valueg
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Ensure that array keys are integers or a strings without ``'.'``.
     *
     * @param  array $array
     * @return bool
     */
    protected function hasValidKeys(array $array)
    {
        foreach ($array as $key => $value) {
            if ((!is_string($key) && !is_int($key)) || (is_string($key) && strpos($key, '.') !== false)) {
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $options = [
            'connections',
            'bindings',
            'instances',
            'searches',
            'auth_sources'
        ];

        $hasValidKeys = function ($array) {
            return $this->hasValidKeys($array);
        };

        $resolver->setDefined($options);
        foreach ($options as $option) {
            $resolver->setAllowedTypes($option, 'array[]');
            $resolver->setAllowedValues($option, $hasValidKeys);
        }
    }

    /**
     * Returns a list of available Ldap connection templates.
     *
     * @return array
     */
    public function getConnectionNames() : array
    {
        return $this->getEntityNames($this->getOption('connections', []), $this->connections);
    }

    /**
     * Returns a list of available Ldap binding templates.
     *
     * @return array
     */
    public function getBindingNames() : array
    {
        return $this->getEntityNames($this->getOption('bindings', []), $this->bindings);
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
        return $this->getEntityNames($this->getOption('instances', []), $this->instances);
    }

    /**
     * Returns a list of available search query names.
     *
     * @return array
     */
    public function getSearchQueryNames() : array
    {
        return $this->getEntityNames($this->getOption('searches', []), $this->queries);
    }

    /**
     * Returns a list of available search query names.
     *
     * @return array
     */
    public function getAuthSourceNames() : array
    {
        return $this->getEntityNames($this->getOption('auth_sources', []), $this->authSources);
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
     * Returns preconfigured instance of ConnectionTemplate.
     *
     * @param string $name
     *
     * @return ConnectionTemplate
     */
    public function getConnection(string $name) : ConnectionTemplate
    {
        if (($this->connections[$name] ?? null) === null) {
            $this->connections[$name] = $this->createConnection($name);
        }
        return $this->connections[$name];
    }

    /**
     * Returns preconfigured instance of BindingTemplate.
     *
     * @param string $name
     *
     * @return BindingTemplate
     */
    public function getBinding(string $name) : BindingTemplate
    {
        if (($this->bindings[$name] ?? null) === null) {
            $this->bindings[$name] = $this->createBinding($name);
        }
        return $this->bindings[$name];
    }

    /**
     * Returns preconfigured instance of LdapInterface.
     *
     * @param  string $name
     * @param  array $arguments
     *
     * @return \Korowai\Lib\Ldap\LdapInterface
     */
    public function getLdapInstance(string $name, array $arguments = []) : LdapInterface
    {
        if (($this->instances[$name] ?? null) === null) {
            $this->instances[$name] = $this->createLdapInstance($name, $arguments);
        }
        return $this->instances[$name];
    }

    /**
     * Returns preconfigured LDAP search query.
     *
     * @param  string $name
     * @return SearchQueryTemplate
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
     */
    public function getAuthSource(string $name) : AuthSource
    {
        if (($this->authSources[$name] ?? null) === null) {
            $this->authSources[$name] = $this->createAuthSource($name);
        }
        return $this->authSources[$name];
    }

    /**
     * Creates and returns an instance of ConnectionTemplate
     *
     * @param  string $name
     * @return ConnectionTemplate
     */
    protected function createConnection(string $name)
    {
        $options = $this->getOptionOrFail('connections.'.$name);
        return new ConnectionTemplate($this, $options);
    }

    /**
     * Creates and returns an instance of BindingTemplate
     *
     * @param  string $name
     * @return BindingTemplate
     */
    protected function createBinding(string $name)
    {
        $options = $this->getOptionOrFail('bindings.'.$name);
        return new BindingTemplate($this, $options);
    }

    /**
     * Creates and returns an instance of LdapInterface.
     *
     * @param  string $name
     * @param  @array $arguments
     * @return \Korowai\Lib\Ldap\LdapInterface
     */
    protected function createLdapInstance(string $name, array $arguments = []) : LdapInterface
    {
        $connectionName = $this->getOptionOrFail('instances.'.$name.'.connection');
        $connection = $this->getConnection($connectionName);

        $ldap = $connection->createLdapInstance();

        if (($bindingName = $this->getOption('instances.'.$name.'.bind')) !== null) {
            $binding = $this->getBinding($bindingName);
            $binding->bindLdapInstance($ldap, $arguments);
        }
        return $ldap;
    }

    /**
     * Creates and returns an instance of SearchQueryTemplate.
     *
     * @param  @array $arguments
     * @return SearchQueryTemplate
     */
    protected function createSearchQuery(string $name) : SearchQueryTemplate
    {
        $options = $this->getOptionOrFail('searches.'.$name);
        return new SearchQueryTemplate($this, $options);
    }

    /**
     * Creates and returns an instance of AuthSource.
     *
     * @param  @array $arguments
     * @return SearchQueryTemplate
     */
    protected function createAuthSource(string $name) : AuthSource
    {
        $options = $this->getOptionOrFail('auth_sources.'.$name);
        return new AuthSource($this, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
