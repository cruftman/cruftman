<?php
/**
 * @file src/Cruftman/Ldap/Presets/Aggregate.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Presets;

use Cruftman\Support\OptionsInterface;
use Cruftman\Support\Traits\HasOptions;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Support\Traits\AggregatesPresets;
use Cruftman\Support\PresetInterface;
use Cruftman\Support\PresetsAggregateInterface;
use Cruftman\Support\Exceptions\OptionNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * LDAP presets' aggregate.
 *
 * The main purpose of this object is to maintain so-called LDAP Presets. An
 * LDAP Preset is an object which encapsulates certain piece of LDAP config
 * array and creates related LDAP service objects. Named presets are registered
 * in LDAP presets' aggregate and may be retrieved by name. Their names in the
 * aggregate correspond to keys found in *$options* array provided to
 * aggregate's ``__construct()``. For example, a configuration item
 * *$options['connections']['foo']* defines a *Connection* preset named ``'foo'``
 * in the aggregate. This *Connection* preset is then available via
 * ``$aggregate->connection('foo')``. An anonymous preset may also be created
 * by providing configuration options (array) instead of name, for example
 * ``$aggregate->connection(['uri' => 'ldap://cruftman.local']);``.
 *
 * The basic concept of LDAP Presets is illustrated with the following example:
 *
 *        vagrant@cruftman:~/code$ php artisan tinker
 *        Psy Shell v0.9.9 (PHP 7.3.9-1+ubuntu18.04.1+deb.sury.org+1 — cli) by Justin Hileman
 *        >>> use Cruftman\Ldap\Presets\Aggregate;
 *        >>> $presets = new Aggregate([
 *        ...     'connections' => [
 *        ...         // An array of Connection presets.
 *        ...         'cruftman' => ['uri' => 'ldap://cruftman.local'],
 *        ...     ],
 *        ...     'bindings' => [
 *        ...         // An array of Binding presets.
 *        ...         'admin' => ['cn=admin,dc=example,dc=org', 'admin'],
 *        ...     ],
 *        ...     'sessions' => [
 *        ...         // An array of Session presets.
 *        ...         'admin@cruftman' => ['connection' => 'cruftman', 'bind' => 'admin'],
 *        ...     ],
 *        ...     'searches' => [
 *        ...         // An array of Search presets.
 *        ...         'person-by-uid' => [
 *        ...           'base' => 'dc=example,dc=org',
 *        ...           'filter' => 'uid=${username}'
 *        ...         ]
 *        ...     ],
 *        ... ]);
 *        => Cruftman\Ldap\Presets\Aggregate {#3073}
 *        >>> $connection = $presets->connection('cruftman');
 *        => Cruftman\Ldap\Presets\Aggregate {#3110}
 *        >>> $connection->substOptions();
 *        => [
 *             "uri" => "ldap://cruftman.local",
 *           ]
 *        >>> $binding = $presets->binding('admin');
 *        => Cruftman\Ldap\Presets\Aggregate {#3103}
 *        >>> $binding->substOptions();
 *        => [
 *             "cn=admin,dc=example,dc=org",
 *             "admin",
 *           ]
 *        >>> $session = $presets->session('admin@cruftman');
 *        => Cruftman\Ldap\Presets\Session {#3101}
 *        >>> $session->substOptions();
 *        => [
 *             "connection" => "cruftman",
 *             "bind" => "admin",
 *           ]
 *        >>> $search = $presets->search('person-by-uid');
 *        => Cruftman\Ldap\Presets\Search {#3095}
 *        >>> $search->substOptions(['username' => 'jsmith']);
 *        => [
 *             "base" => "dc=example,dc=org",
 *             "filter" => "uid=jsmith",
 *           ]
 *
 * Example presets provided by LDAP Service include:
 *
 * - <a href="Connection.html">Connection</a>
 *
 *      Encapsulates configuration parameters, such as *uri*, necessary to
 *      create new instances of *LdapInterface*. Also, provides a method to
 *      spawn these instances out of the box.
 *
 * - <a href="Binding.html">Binding</a>
 *
 *      Encapsulates configuration parameters (bind DN, password) that may be
 *      used to perform LDAP bind on existing instances of *LdapInterface*.
 *      Also, provides a method to perform these binds.
 *
 * - <a href="Session.html">Session</a>
 *
 *      Encapsulates references to one connection and one binding preset. Also,
 *      provides a method to create instances of *LdapInterface* that are already
 *      bound using the binding preset.
 *
 * - <a href="Search.html">Search</a>
 *
 *      Encapsulates an array of options necessary to define an LDAP search.
 *      The options include base DN, search filter and other search options.
 *      Also, provides a method to create instances of *SearchQueryInterface*.
 *
 * - <a href="AuthSource.html">AuthSource</a>
 */
class Aggregate implements OptionsInterface, PresetsAggregateInterface
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
    public function __construct(array $options = [], string $prefix = "ldap")
    {
        $this->setOptionsPrefix($prefix)->setOptions($options);
    }

    /**
     * Configure $resolver to check validity of the $options provided to __construct().
     *
     * @param  OptionsResolver $resolver
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $this->configurePresetOptionsResolver($resolver);
        $resolver->setDefined(['auth_schema'])
                 ->setAllowedTypes('auth_schema', 'array');
    }

    /**
     * Returns an array that maps preset classes onto their keys in the configuration array.
     *
     * Implemented here to fulfill requirements of *AggregatesPresets* trait.
     *
     * @return array
     * @see \Cruftman\Support\Traits\AggregatesPresets
     */
    protected function getPresetKeysByClasses() : array
    {
        return $this->presetKeysByClasses;
    }

    /**
     * Tells whether the *$class* is a singleton preset.
     *
     * Implemented here to fulfill requirements of *AggregatesPresets* trait.
     *
     * @param string $class
     * @return bool|null
     * @see \Cruftman\Support\Traits\AggregatesPresets
     */
    public function isSingletonPreset(string $class) : ?bool
    {
        return array_key_exists($class, $this->getPresetKeysByClasses()) ? ($class === AuthSchema::class) : null;
    }

    /**
     * Given an array of preset *$options* creates an instance of **preset** *$class*.
     *
     * Implemented here to fulfill requirements of *AggregatesPresets* trait.
     *
     * @param string $class
     * @return PresetInterface
     * @see \Cruftman\Support\Traits\AggregatesPresets
     */
    protected function createPresetWithOptions(string $class, array $options) : PresetInterface
    {
        return new $class($options, $this);
    }

    /**
     * Returns a list of available connection presets.
     *
     * @return string[]
     */
    public function connections() : array
    {
        return $this->getNamedPresetsNames(Connection::class);
    }

    /**
     * Returns a list of available binding presets.
     *
     * @return string[]
     */
    public function bindings() : array
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
    public function sessions() : array
    {
        return $this->getNamedPresetsNames(Session::class);
    }

    /**
     * Returns a list of available search query presets.
     *
     * @return string[]
     */
    public function searches() : array
    {
        return $this->getNamedPresetsNames(Search::class);
    }

    /**
     * Returns a list of available authentication attempt presets.
     *
     * @return string[]
     */
    public function authAttempts() : array
    {
        return $this->getNamedPresetsNames(AuthAttempt::class);
    }

    /**
     * Returns a list of available authentication source presets.
     *
     * @return string[]
     */
    public function authSources() : array
    {
        return $this->getNamedPresetsNames(AuthSource::class);
    }

    /**
     * Returns a Connection preset.
     *
     * @param string|array $options
     * @return Connection
     * @throws OptionNotFoundException
     */
    public function connection($options) : Connection
    {
        return $this->getNamedPreset(Connection::class, $options);
    }

    /**
     * Returns a Binding preset.
     *
     * @param string|array $options
     * @return Binding
     * @throws OptionNotFoundException
     */
    public function binding($options) : Binding
    {
        return $this->getNamedPreset(Binding::class, $options);
    }

    /**
     * Returns an Session preset.
     *
     * @param  string|array $options
     * @return Session
     * @throws OptionNotFoundException
     */
    public function session($options) : Session
    {
        return $this->getNamedPreset(Session::class, $options);
    }

    /**
     * Returns a Search preset.
     *
     * @param  string|array $options
     * @return Search
     * @throws OptionNotFoundException
     */
    public function search($options) : Search
    {
        return $this->getNamedPreset(Search::class, $options);
    }

    /**
     * Returns an AuthAttempt preset.
     *
     * @param  string|array $options
     * @return AuthAttempt
     * @throws OptionNotFoundException
     */
    public function authAttempt($options) : AuthAttempt
    {
        return $this->getNamedPreset(AuthAttempt::class, $options);
    }

    /**
     * Returns an AuthSource preset.
     *
     * @param  string|array $options
     * @return AuthSource
     * @throws OptionNotFoundException
     */
    public function authSource($options) : AuthSource
    {
        return $this->getNamedPreset(AuthSource::class, $options);
    }

    /**
     * Returns an Auth preset.
     *
     * @return AuthSchema
     * @throws OptionNotFoundException
     */
    public function authSchema() : AuthSchema
    {
        return $this->getSingletonPreset(AuthSchema::class);
    }
}

// vim: syntax=php sw=4 ts=4 et:
