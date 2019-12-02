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

use Cruftman\Support\OptionsInterface;
use Cruftman\Support\Traits\HasOptions;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Support\Traits\AggregatesPresets;
use Cruftman\Support\PresetInterface;
use Cruftman\Support\PresetsAggregateInterface as PresetsAggregateInterface;
use Cruftman\Ldap\Preset\AuthAttempt;
use Cruftman\Ldap\Preset\AuthSchema;
use Cruftman\Ldap\Preset\AuthSource;
use Cruftman\Ldap\Preset\Binding;
use Cruftman\Ldap\Preset\Connection;
use Cruftman\Ldap\Preset\Session;
use Cruftman\Ldap\Preset\Search;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Cruftman's LDAP service.
 *
 * The main purpose of LDAP Service is to maintain so-called Presets. A Preset
 * is an object which encapsulates certain piece of LDAP config array and
 * creates related LDAP service objects. Named presets are registered in LDAP
 * Service and may be retrieved by name. Their names in LDAP Service correspond
 * to keys found in *$options* array provided to Service's ``__construct()``.
 * For example, a configuration item *$options['connections']['foo']* defines a
 * Connection preset named ``'foo'`` in LDAP Service. This Connection preset is
 * then available via ``$service->connection('foo')``. An anonymous preset may
 * also be created by providing configuration options (array) instead of name,
 * for example ``$service->connection(['uri' => 'ldap://cruftman.local']);``.
 *
 * The basic concept of LDAP Service Presets is illustrated with the following
 * example:
 *
 * ```
 *    vagrant@cruftman:~/code$ php artisan tinker
 *    Psy Shell v0.9.9 (PHP 7.3.9-1+ubuntu18.04.1+deb.sury.org+1 — cli) by Justin Hileman
 *    >>> use Cruftman\Ldap\Service;
 *    >>> $service = new Service([
 *    ...     'connections' => [
 *    ...         // An array of Connection presets.
 *    ...         'cruftman' => ['uri' => 'ldap://cruftman.local'],
 *    ...     ],
 *    ...     'bindings' => [
 *    ...         // An array of Binding presets.
 *    ...         'admin' => ['cn=admin,dc=example,dc=org', 'admin'],
 *    ...     ],
 *    ...     'sessions' => [
 *    ...         // An array of Session presets.
 *    ...         'admin@cruftman' => ['connection' => 'cruftman', 'bind' => 'admin'],
 *    ...     ],
 *    ...     'searches' => [
 *    ...         // An array of Search presets.
 *    ...         'person-by-uid' => [
 *    ...           'base' => 'dc=example,dc=org',
 *    ...           'filter' => 'uid=${username}'
 *    ...         ]
 *    ...     ]
 *    ... ]);
 *    => Cruftman\Ldap\Service {#3073}
 *    >>> $connection = $service->connection('cruftman');
 *    => Cruftman\Ldap\Preset\Connection {#3110}
 *    >>> $connection->substOptions();
 *    => [
 *         "uri" => "ldap://cruftman.local",
 *       ]
 *    >>> $binding = $service->binding('admin');
 *    => Cruftman\Ldap\Preset\Binding {#3103}
 *    >>> $binding->substOptions();
 *    => [
 *         "cn=admin,dc=example,dc=org",
 *         "admin",
 *       ]
 *    >>> $session = $service->session('admin@cruftman');
 *    => Cruftman\Ldap\Preset\Session {#3101}
 *    >>> $session->substOptions();
 *    => [
 *         "connection" => "cruftman",
 *         "bind" => "admin",
 *       ]
 *    >>> $search = $service->search('person-by-uid');
 *    => Cruftman\Ldap\Preset\Search {#3095}
 *    >>> $search->substOptions(['username' => 'jsmith']);
 *    => [
 *         "base" => "dc=example,dc=org",
 *         "filter" => "uid=jsmith",
 *       ]
 *    >>> $ldap = $session->createLdap();
 *    => Korowai\Lib\Ldap\Ldap {#3120}
 *    >>> $query = $search->createQuery($ldap, ['username' => 'jsmith']);
 *    => Korowai\Lib\Ldap\Adapter\ExtLdap\SearchQuery {#3086}
 *    >>> $query->getResult()->getEntries();
 *    => [
 *         "uid=jsmith,ou=people,dc=example,dc=org" => Korowai\Lib\Ldap\Entry {#3096},
 *       ]
 * ```
 *
 * Example presets provided by LDAP Service include:
 *
 * - <a href="Preset/Connection.html">Connection</a>
 *
 *      Encapsulates configuration parameters, such as *uri*, necessary to
 *      create new instances of *LdapInterface*. Also, provides a method to
 *      spawn these instances out of the box.
 *
 * - <a href="Preset/Binding.html">Binding</a>
 *
 *      Encapsulates configuration parameters (bind DN, password) that may be
 *      used to perform LDAP bind on existing instances of *LdapInterface*.
 *      Also, provides a method to perform these binds.
 *
 * - <a href="Preset/Session.html">Session</a>
 *
 *      Encapsulates references to one connection and one binding preset. Also,
 *      provides a method to create instances of *LdapInterface* that are already
 *      bound using the binding preset.
 *
 * - <a href="Preset/Search.html">Search</a>
 *
 *      Encapsulates an array of options necessary to define an LDAP search.
 *      The options include base DN, search filter and other search options.
 *      Also, provides a method to create instances of *SearchQueryInterface*.
 *
 * - <a href="Preset/AuthSource.html">AuthSource</a>
 */
class Service implements OptionsInterface, PresetsAggregateInterface
{
    use AggregatesPresets,
        HasOptions,
        ValidatesOptions;

    /**
     * Maps presets' class names onto keys in *$options*.
     *
     * @var array
     */
    protected $presetKeysByClasses = [
        Connection::class => 'connections',
        Binding::class => 'bindings',
        Session::class => 'sessions',
        Search::class => 'searches',
        AuthAttempt::class => 'auth_attempts',
        AuthSource::class => 'auth_sources',
        AuthSchema::class => 'auth_schema'
    ];

    /**
     * Initializes the service object.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Configure $resolver to check validity of the $options provided to __construct().
     *
     * @param  OptionsResolver $resolver
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $this->configurePresetOptionsResolver($resolver);
        $resolver->setRequired(['auth_schema'])
                 ->setAllowedTypes('auth_schema', 'array');
    }

    /**
     * @todo Write documentation
     * @return array
     */
    protected function getPresetKeysByClasses() : array
    {
        return $this->presetKeysByClasses;
    }

    /**
     * @todo Write documentation
     * @return bool
     */
    public function isSingletonPreset(string $class) : ?bool
    {
        return array_key_exists($class, $this->getPresetKeysByClasses()) ? ($class === AuthSchema::class) : null;
    }

    /**
     * @todo Write documentation
     * @return PresetInterface
     */
    protected function createPresetWithOptions(string $class, array $options) : PresetInterface
    {
        return new $class($this, $options);
    }

//    /**
//     * @todo Write documentation
//     * @return array
//     */
//    public function getPresetsByClasses() : array
//    {
//        return $this->presetsByClasses;
//    }

    public function setPresetByName(string $class, string $name, PresetInterface $preset)
    {
        $this->presetsByClasses[$class][$name] = $preset;
    }

    /**
     * Returns a list of available connection presets.
     *
     * @return string[]
     */
    public function getConnections() : array
    {
        return $this->getNamedPresetsNames(Connection::class);
    }

    /**
     * Returns a list of available binding presets.
     *
     * @return string[]
     */
    public function getBindings() : array
    {
        return $this->getNamedPresetsNames(Binding::class);
    }

    /**
     * Returns a list of available session presets.
     *
     * The returned array includes names of already created Session instances as
     * well as those defined in config but not yet created.
     *
     * @return string[]
     */
    public function getSessions() : array
    {
        return $this->getNamedPresetsNames(Session::class);
    }

    /**
     * Returns a list of available search query presets.
     *
     * @return string[]
     */
    public function getSearches() : array
    {
        return $this->getNamedPresetsNames(Search::class);
    }

    /**
     * Returns a list of available authentication attempt presets.
     *
     * @return string[]
     */
    public function getAuthAttempts() : array
    {
        return $this->getNamedPresetsNames(AuthAttempt::class);
    }

    /**
     * Returns a list of available authentication source presets.
     *
     * @return string[]
     */
    public function getAuthSources() : array
    {
        return $this->getNamedPresetsNames(AuthSource::class);
    }

    /**
     * Returns a Connection preset.
     *
     * @param string|array $options
     * @return Connection
     */
    public function getConnection($options) : Connection
    {
        return $this->getNamedPreset(Connection::class, $options);
    }

    /**
     * Returns a Binding preset.
     *
     * @param string|array $options
     * @return Binding
     */
    public function getBinding($options) : Binding
    {
        return $this->getNamedPreset(Binding::class, $options);
    }

    /**
     * Returns an Session preset.
     *
     * @param  string|array $options
     * @return Session
     */
    public function getSession($options) : Session
    {
        return $this->getNamedPreset(Session::class, $options);
    }

    /**
     * Returns a Search preset.
     *
     * @param  string|array $options
     * @return Search
     */
    public function getSearch($options) : Search
    {
        return $this->getNamedPreset(Search::class, $options);
    }

    /**
     * Returns an AuthAttempt preset.
     *
     * @param  string|array $options
     * @return AuthAttempt
     */
    public function getAuthAttempt($options) : AuthAttempt
    {
        return $this->getNamedPreset(AuthAttempt::class, $options);
    }

    /**
     * Returns an AuthSource preset.
     *
     * @param  string|array $options
     * @return AuthSource
     */
    public function getAuthSource($options) : AuthSource
    {
        return $this->getNamedPreset(AuthSource::class, $options);
    }

    /**
     * Returns an Auth preset.
     *
     * @return AuthSchema
     */
    public function getAuthSchema() : AuthSchema
    {
        return $this->getSingletonPreset(AuthSchema::class);
    }
}

// vim: syntax=php sw=4 ts=4 et:
