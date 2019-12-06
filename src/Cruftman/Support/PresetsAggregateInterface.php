<?php
/**
 * @file src/Cruftman/Support/PresetsAggregateInterface.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support;

/**
 * Interface for **presets aggregate** objects.
 *
 * A **presets aggregate** acts as a container for
 * <a href="PresetInterface.html">preset</a> objects.
 * It classifies presets using PHP class names &mdash;
 * any class that implements
 * <a href="PresetInterface.html">PresetInterface</a>
 * may be potentially handled by a **presets aggregate**.
 *
 * **Preset** objects represent sections of a configuration array, while
 * **presets aggregate** represents the whole array containing these
 * sections. Consider the following configuration array, as an example
 *
 * ```
 *  [
 *      // An array of named Connection presets.
 *      'connections' => [
 *          'ldap1' => ['url' => 'ldaps://ldap1.example.com'],
 *          'ldap2' => ['url' => 'ldaps://ldap2.example.com'],
 *      ],
 *
 *      // An array of named Binding presets.
 *      'bindings' => [
 *          'admin'  => ['cn=admin,dc=example,dc=org', 'adminpassword'],
 *          'jsmith' => ['uid=jsmith,ou=peple,dc=example,dc=org', 'jsmithpassword'],
 *      ],
 *
 *      // An array of named Search presets.
 *      'searches' => [
 *          'user-by-name' => [
 *              'base' => 'ou=people,dc=example,dc=org',
 *              'filter' => '(uid=${username})',
 *              'options' => ['base' => 'one']
 *          ]
 *      ],
 *
 *      // An array of named Session presets.
 *      'sessions' => [
 *          'admin@ldap1' => [ 'connection' => 'ldap1', 'binding' => 'admin' ],
 *          'admin@ldap2' => [ 'connection' => 'ldap2', 'binding' => 'admin' ],
 *      ],
 *
 *      // An array of named AuthSource presets.
 *      'auth_sources' => [
 *          'users' => [
 *              'sessions' => ['admin@ldap1', 'admin@ldap2'],
 *              'search' => 'user-by-name',
 *              // ...
 *          ]
 *      ],
 *
 *      // A singleton AuthSchema preset.
 *      'auth_schema' => [
 *          'sources' => ['users'],
 *          // ...
 *      ]
 *  ]
 * ```
 * The above configuration can be modelled as a **presets aggregate**. Assume,
 * we have preset classes named ``Connection``, ``Binding``, ``Search``,
 * ``Session``, ``AuthSource`` and ``AuthSchema``. Instances of these classes
 * may be maintained by **presets aggregate** such that they get initialized
 * according to the corresponding sections of the configuration array
 * (``'connections'``, ``'binding'``, ..., ``'auth_schema'``).
 *
 * Sections such as ``'connections'``, ``'bindings'``, ..., ``auth_sources``
 * (all plural) are modelled as arrays of **named presets** of given type. For
 * example, section of ``'connections'`` yields two ``Connection`` presets, one
 * named ``ldap1`` and the other named ``ldap2``. They may be used as follows
 *
 * ```
 *  // $presets is an instance of PresetsAggregateInterface
 *
 *  $ldap1 = $presets->getNamedPreset(Connection::class, "ldap1");
 *  assert($ldap1->getOptions() === ['url' => 'ldaps://ldap1.example.com']);
 *
 *  $ldap2 = $presets->getNamedPreset(Connection::class, "ldap2");
 *  assert($ldap2->getOptions() === ['url' => 'ldaps://ldap2.example.com']);
 * ```
 *
 * A configuration section can define multiple **named presets** of given type.
 * They may be referred from within other presets by their names. In the
 * following snippet, we have two ``Connection`` presets under
 * ``'connections'`` with names ``'ldap1'`` and ``'ldap2'``. A ``Session``
 * preset named ``'admin@ldap1'`` refers the ``Connection`` preset named
 * ``ldap1``.
 *
 * ```
 *      'connections' => [
 *          'ldap1' => ['url' => 'ldaps://ldap1.example.com'],
 *          'ldap2' => ['url' => 'ldaps://ldap2.example.com'],
 *      ],
 *
 *      'sessions' => [
 *          'admin@ldap1' => [ 'connection' => 'ldap1', ... ],
 *      ],
 * ```
 *
 * Available names of **named presets** of given type may be queried with
 * ``getNamedPresetsNames()``:
 *
 * ```
 *  $connections = $presets->getNamedPresetsNames(Connection::class);
 * ```
 *
 * Available preset classes supported by a given **presets aggregate** may be
 * queried with ``getPresetClasses()``:
 * ```
 *  $classes = $presets->getPresetClasses();
 * ```
 *
 * The last section of our configuration array, ``auth_schema``, is an example
 * of a **singleton preset**. Only one instance of **singleton preset** of
 * given type is maintained by a **presets aggregate** object.
 *
 * ```
 *  $authSchema = $presets->getSingletonPreset(AuthSchema::class);
 * ```
 *
 * Whether a class represents **singleton preset** is indicated by
 * ``isSingletonPreset()`` method:
 *
 * ```
 *  assert($presets->isSingletonPreset(Connection::class) === false);
 *  assert($presets->isSingletonPreset(AuthSchema::class) === true);
 *  assert($presets->isSingletonPreset('UnknownClass') === null);
 * ```
 *
 * Preset objects of particular types get initialized according to predefined
 * sections of the configuration array. For example, options for ``Connection``
 * presets are taken from ``'connections'`` section. The section names (keys)
 * for particular preset classes may be retrieved using
 * ``getPresetOptionsKey()`` method:
 *
 * ```
 *  // let $presets be our an instance of PresetsAggregateInterface
 *
 *  //  Connection
 *  assert($presets->getPresetOptionsKey(Connection::class) === 'connections');
 *
 *  //  ...
 *
 *  //  AuthSource
 *  assert($presets->getPresetOptionsKey(AuthSource::class) === 'auth_sources');
 *
 *  //  AuthSchema
 *  assert($presets->getPresetOptionsKey(AuthSchema::class) === 'auth_sources');
 * ```
 */
interface PresetsAggregateInterface
{
    /**
     * Returns an array of *preset* class names supported by this aggregate.
     *
     * @return array
     */
    public function getPresetClasses() : array;

    /**
     * Tells whether the *$class* is a singleton preset type.
     *
     * @param string $class
     * @return bool|null ``true`` if *$class* is a singleton **preset** class,
     *                   ``false`` if *$class* is a non-singleton **preset** class,
     *                   and ``null`` if *$class* is not a **preset** class supported
     *                   by this **preset aggregate**.
     */
    public function isSingletonPreset(string $class) : ?bool;

    /**
     * Returns an array of available presets' names of *$class* type.
     *
     * @param  string $class
     * @return array
     */
    public function getNamedPresetsNames(string $class) : array;

    /**
     * Returns a named preset of a given type.
     *
     * If *$options* is a string, it's assumed to be a preset name. Otherwise,
     * *$options* are assumed to be an array which provides preset's options
     * used to configure the preset being returned.
     *
     * @param  string $class
     * @param  string|array $options
     * @return PresetInterface|null
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    public function getNamedPreset(string $class, $options) : ?PresetInterface;

    /**
     * Returns a singleton preset of a given type.
     *
     * @param  string $class
     * @param  array|null $options
     * @return PresetInterface|null
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    public function getSingletonPreset(string $class, ?array $options = null) : ?PresetInterface;
}

// vim: syntax=php sw=4 ts=4 et:
