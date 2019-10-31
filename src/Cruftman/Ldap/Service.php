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
     * Check if ``$arg`` may be safely used as array key in a laravel
     * configuration file (it's integer or a string without dots).
     *
     * @param  mixed $arg
     * @return bool
     */
    protected function isValidOptionKey($arg)
    {
        return is_int($arg) || (is_string($arg) && strpos($arg, '.') === false);
    }

    /**
     * Check if ``$array`` keys can be safely used as array keys in a laravel
     * configuration file (e.g. they don't have dots).
     *
     * @param  array $array
     * @return bool
     */
    protected function allKeysAreValidOptionKeys(array $array)
    {
        foreach ($array as $key => $value) {
            if (!$this->isValidOptionKey($key)) {
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

        $resolver->setDefined($options);
        foreach ($options as $option) {
            $resolver->setAllowedTypes($option, 'array[]');
            $resolver->setAllowedValues($option, function ($array) {
                return $this->allKeysAreValidOptionKeys($array);
            });
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
     * Returns preconfigured instance of ConnectionTemplate.
     *
     * @param string $name
     *
     * @return ConnectionTemplate
     */
    public function getConnection(string $name) : ConnectionTemplate
    {
        return $this->getNamedEntity($name, $this->connections, 'connections', ConnectionTemplate::class);
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
        return $this->getNamedEntity($name, $this->bindings, 'bindings', BindingTemplate::class);
    }

    /**
     * Returns preconfigured instance of LdapInterface.
     *
     * @param  string $name
     * @param  array $arguments
     *
     * @return \Korowai\Lib\Ldap\LdapInterface
     */
    public function getLdapInstance(string $name) : LdapInstance
    {
        return $this->getNamedEntity($name, $this->instances, 'instances', LdapInstance::class);
    }

    /**
     * Returns preconfigured LDAP search query.
     *
     * @param  string $name
     * @return SearchQueryTemplate
     */
    public function getSearchQuery(string $name) : SearchQueryTemplate
    {
        return $this->getNamedEntity($name, $this->queries, 'searches', SearchQueryTemplate::class);
    }

    /**
     * Returns preconfigured LDAP search query.
     *
     * @param  string $name
     * @return AuthSource
     */
    public function getAuthSource(string $name) : AuthSource
    {
        return $this->getNamedEntity($name, $this->authSources, 'auth_sources', AuthSource::class);
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
     * Returns a named entity such as LdapInstance, ConnectionTemplate etc.
     *
     * The entity gets created when it's requested for the first time.
     *
     * @param  string $name
     * @param  array  $registry
     * @param  string $scope
     * @param  string $class
     * @return object
     */
    protected function getNamedEntity(string $name, array &$registry, string $scope, string $class)
    {
        if (($registry[$name] ?? null) === null) {
            $registry[$name] = $this->createNamedEntity($scope, $name, $class);
        }
        return $registry[$name];
    }

    /**
     * Creates a named entity.
     *
     * @param  string $scope
     * @param  string $name
     * @param  string $class
     * @return object
     */
    protected function createNamedEntity(string $scope, string $name, string $class)
    {
        $options = $this->getOptionOrFail($scope.'.'.$name);
        return new $class($this, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
