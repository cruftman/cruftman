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
use Cruftman\Ldap\Traits\HasPresets;
use Cruftman\Ldap\Preset\AuthRequest;
use Cruftman\Ldap\Preset\AuthSource;
use Cruftman\Ldap\Preset\Binding;
use Cruftman\Ldap\Preset\Connection;
use Cruftman\Ldap\Preset\Ldap;
use Cruftman\Ldap\Preset\SearchQuery;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Cruftman's LDAP service.
 *
 * The main purpose of LDAP Service is to maintain so-called Presets. A Preset
 * is an object which encapsulates certain piece of LDAP config array and
 * provides related service. Presets are registered in LDAP Service and
 * may be retrieved by name. Their names in LDAP Service correspond to keys
 * found in *$options* array provided to Service's ``__construct()``. For
 * example, a configuration item *$options['connections']['foo']* defines a
 * Connection preset named ``'foo'`` in LDAP Service. This Connection preset
 * is then available via ``$service->connection('foo')``.
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
 *    ...     'instances' => [
 *    ...         // An array of Ldap presets.
 *    ...         'admin@cruftman' => ['connection' => 'cruftman', 'bind' => 'admin'],
 *    ...     ]
 *    ... ]);
 *    => Cruftman\Ldap\Service {#3073}
 *    >>> $service->connection('cruftman');
 *    => Cruftman\Ldap\Preset\Connection {#3100}
 *    >>> $service->connection('cruftman')->substOptions();
 *    => [
 *         "uri" => "ldap://cruftman.local",
 *       ]
 *    >>> $service->binding('admin');
 *    => Cruftman\Ldap\Preset\Binding {#3101}
 *    >>> $service->binding('admin')->substOptions();
 *    => [
 *         "cn=admin,dc=example,dc=org",
 *         "admin",
 *       ]
 *    >>> $service->ldap('admin@cruftman');
 *    => Cruftman\Ldap\Preset\Ldap {#3104}
 *    >>> $service->ldap('admin@cruftman')->substOptions();
 *    => [
 *         "connection" => "cruftman",
 *         "bind" => "admin",
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
 * - <a href="Preset/Ldap.html">Ldap</a>
 *
 *      Encapsulates references to one connection and one binding preset. Also,
 *      provides a method to create instances of *LdapInterface* that are already
 *      bound using the binding preset.
 *
 * - <a href="Preset/SearchQuery.html">SearchQuery</a>
 *
 *      Encapsulates an array of options necessary to define an LDAP search.
 *      The options include a reference to an Ldap preset, and all the query
 *      parameters such as base DN, search filter and so on. Also, provides
 *      a method to perform that query.
 *
 * - <a href="Preset/AuthSource.html">AuthSource</a>
 */
class Service implements OptionsInterface
{
    use HasPresets,
        HasOptions,
        ValidatesOptions;

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
     * Configure $resolver to check validity of the $options provided to __construct().
     *
     * @param  OptionsResolver $resolver
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $this->configurePresetOptionsResolver($resolver);
    }

    /**
     * Returns a list of available connection presets.
     *
     * @return array
     */
    public function connections() : array
    {
        return $this->getPresets(Connection::class);
    }

    /**
     * Returns a list of available binding presets.
     *
     * @return array
     */
    public function bindings() : array
    {
        return $this->getPresets(Binding::class);
    }

    /**
     * Returns a list of available ldap presets.
     *
     * The returned array includes names of already created Ldap instances as
     * well as those defined in config but not yet created.
     *
     * @return array
     */
    public function ldaps() : array
    {
        return $this->getPresets(Ldap::class);
    }

    /**
     * Returns a list of available search query presets.
     *
     * @return array
     */
    public function searchQueries() : array
    {
        return $this->getPresets(SearchQuery::class);
    }

    /**
     * Returns a list of available authentication source presets.
     *
     * @return array
     */
    public function authSources() : array
    {
        return $this->getPresets(AuthSource::class);
    }

    /**
     * Returns a Connection preset.
     *
     * @param string $name
     *
     * @return Connection
     */
    public function connection(string $name) : Connection
    {
        return $this->getPreset(Connection::class, $name);
    }

    /**
     * Returns a Binding preset.
     *
     * @param string $name
     *
     * @return Binding
     */
    public function binding(string $name) : Binding
    {
        return $this->getPreset(Binding::class, $name);
    }

    /**
     * Returns an Ldap preset.
     *
     * @param  string $name
     * @param  array $arguments
     *
     * @return Ldap
     */
    public function ldap(string $name) : Ldap
    {
        return $this->getPreset(Ldap::class, $name);
    }

    /**
     * Returns a SearchQuery preset.
     *
     * @param  string $name
     * @return SearchQuery
     */
    public function searchQuery(string $name) : SearchQuery
    {
        return $this->getPreset(SearchQuery::class, $name);
    }

    /**
     * Returns an AuthSource preset.
     *
     * @param  string $name
     * @return AuthSource
     */
    public function authSource(string $name) : AuthSource
    {
        return $this->getPreset(AuthSource::class, $name);
    }
}

// vim: syntax=php sw=4 ts=4 et:
