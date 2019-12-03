<?php
/**
 * @file src/Cruftman/Support/Traits/AggregatesPresets.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support\Traits;

use Cruftman\Support\OptionsInterface;
use Cruftman\Support\Traits\HasOptions;
use Cruftman\Support\PresetInterface;
use Cruftman\Support\Exceptions\PresetException;
use Cruftman\Support\Exceptions\OptionNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Implements most of **presets aggregate** methods.
 *
 * The receiving class must define all the abstract methods:
 *
 *  - ``getOption()``, ``getOptionOrFail()``,
 *  - ``getPresetKeysByClasses()``,
 *  - ``isSingletonPreset()``,
 *  - ``createPresetWithOptions()``.
 *
 */
trait AggregatesPresets
{
    /**
     * Returns an option's value from configuration array used to setup this aggregate.
     *
     * @param string $name option name
     * @return mixed
     */
    abstract function getOption(string $name);

    /**
     * Returns an option's value from configuration array used to setup this aggregate.
     *
     * @param string $name option name
     * @throws OptionNotFoundException should be thrown when requested a nonexistent option.
     */
    abstract function getOptionOrFail(string $name);

    /**
     * Returns an array that maps preset classes into their keys in the configuration array.
     *
     * This defines correspondence between classes and sections of configuration array.
     *
     * @return array
     */
    abstract function getPresetKeysByClasses() : array;

    /**
     * Tells whether the *$class* is a singleton preset.
     *
     * @param string $class
     * @return bool|null ``true`` if *$class* is a singleton **preset** class,
     *                   ``false`` if *$class* is a non-singleton **preset** class,
     *                   and ``null`` if *$class* is not a **preset** class supported
     *                   by this **preset aggregate**.
     */
    abstract function isSingletonPreset(string $class) : ?bool;

    /**
     * Given an array of preset *$options* creates an instance of **preset** *$class*.
     *
     * @param string $class
     * @return PresetInterface
     */
    abstract function createPresetWithOptions(string $class, array $options) : PresetInterface;

    /**
     * An array of instantiated preset objects by type.
     *
     * @var array
     */
    protected $presetsByClasses = [];

    /**
     * Check if ``$arg`` may be safely used as array key in a laravel
     * configuration file (it's integer or a string without dots).
     *
     * @param  mixed $arg
     * @return bool
     */
    protected function isValidOptionKey($arg) : bool
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
    protected function allKeysAreValidOptionKeys(array $array) : bool
    {
        foreach ($array as $key => $value) {
            if (!$this->isValidOptionKey($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Configure $resolver to check validity of the preset-related $options
     * provided to __construct().
     *
     * @param  OptionsResolver $resolver
     */
    public function configurePresetOptionsResolver(OptionsResolver $resolver)
    {
        $keysByClasses = $this->getPresetKeysByClasses();
        $options = array_unique(array_values($keysByClasses));
        $resolver->setDefined($options);
        foreach ($keysByClasses as $class => $key) {
            if ($this->isSingletonPreset($class) === false) {
                $resolver->setAllowedTypes($key, 'array[]');
            }
            $resolver->setAllowedValues($key, function ($array) {
                return $this->allKeysAreValidOptionKeys($array);
            });
        }
    }

    /**
     * Returns an array of *preset* class names supported by this aggregate.
     *
     * @return array
     */
    public function getPresetClasses() : array
    {
        return array_keys($this->getPresetKeysByClasses());
    }

    /**
     * Get a root key to options array for given preset class.
     *
     * @param  string $class
     * @return string|null
     */
    public function getPresetOptionsKey(string $class) : ?string
    {
        return ($this->getPresetKeysByClasses())[$class] ?? null;
    }

    /**
     * Get a root key to options array for given preset class or throw an
     * exception if it's not defined.
     *
     * @param  string $class
     * @return string
     * @throws PresetException
     */
    public function getPresetOptionsKeyOrFail(string $class) : string
    {
        if (($key = $this->getPresetOptionsKey($class)) === null) {
            throw new PresetException('"'.$class.'" is not a supported preset class');
        }
        return $key;
    }

    /**
     * Returns options encapsulated by a preset (named).
     *
     * @param string $class preset class
     * @param string $name preset name
     * @return array|null
     */
    public function getNamedPresetOptions(string $class, string $name) : ?array
    {
        if ($this->isSingletonPreset($class)) {
            return null;
        }
        $key = $this->getPresetOptionsKey($class);
        return $key === null ? null : $this->getOption($key.'.'.$name);
    }

    /**
     * Returns options encapsulated by a preset (named).
     *
     * @param string $class preset class
     * @param string $name preset name
     * @return array
     * @throws PresetException when *$class* is a singleton preset or is not supported by this aggregate
     * @throws OptionNotFoundException when there is no configuration option for the requested preset
     */
    public function getNamedPresetOptionsOrFail(string $class, string $name) : array
    {
        if ($this->isSingletonPreset($class)) {
            throw new PresetException('"'.$class.'" is a singleton preset');
        }
        $key = $this->getPresetOptionsKeyOrFail($class);
        return $this->getOptionOrFail($key.'.'.$name);
    }

    /**
     * Returns options encapsulated by a preset (singleton).
     *
     * @param string $class preset class
     * @return array|null
     */
    public function getSingletonPresetOptions(string $class) : ?array
    {
        if ($this->isSingletonPreset($class) === false) {
            return null;
        }
        $key = $this->getPresetOptionsKey($class);
        return $key === null ? null : $this->getOption($key);
    }

    /**
     * Returns options encapsulated by a preset (singleton).
     *
     * @param string $class preset class
     * @return array
     * @throws PresetException when *$class* is a non-singleton preset or is not supported by this aggregate
     * @throws OptionNotFoundException when there is no configuration option for the requested preset
     */
    public function getSingletonPresetOptionsOrFail(string $class) : array
    {
        if ($this->isSingletonPreset($class) === false) {
            throw new PresetException('"'.$class.'" is not a singleton preset');
        }
        $key = $this->getPresetOptionsKeyOrFail($class);
        return $this->getOptionOrFail($key);
    }

    /**
     * Generates an array of presets' names for a given preset type.
     *
     * @param  string $class
     * @return array
     */
    public function getNamedPresetsNames(string $class) : array
    {
        $key = $this->getPresetOptionsKeyOrFail($class);
        $optionKeys = array_keys($this->getOption($key, []));
        $presetKeys = array_keys($this->presetsByClasses[$class] ?? []);

        return array_unique(array_merge($optionKeys, $presetKeys));
    }

    /**
     * Returns a named preset of a given type.
     *
     * @param  string $class
     * @param  string|array $options
     * @return PresetInterface|null
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    public function getNamedPreset(string $class, $options) : ?PresetInterface
    {
        if (is_string($options)) {
            return $this->getNamedPresetByName($class, $options);
        } else {
            return $this->createNamedPreset($class, $options);
        }
    }

    /**
     * Returns a named preset of a given type.
     *
     * The preset object gets created when it's requested for the first time.
     *
     * @param  string $class
     * @param  string $name
     * @return PresetInterface|null
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    protected function getNamedPresetByName(string $class, string $name) : ?PresetInterface
    {
        if (($this->presetsByClasses[$class][$name] ?? null) === null) {
            $preset = $this->createNamedPreset($class, $name);
            $this->presetsByClasses[$class][$name] = $preset;
        }
        return $this->presetsByClasses[$class][$name];
    }

    /**
     * Creates a named preset object.
     *
     * @param  string $class
     * @param  string|array $options
     * @return PresetInterface|null
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    protected function createNamedPreset(string $class, $options) : ?PresetInterface
    {
        if (is_string($options)) {
            $options = $this->getNamedPresetOptionsOrFail($class, $options);
        }
        return $this->createPresetWithOptions($class, $options);
    }

    /**
     * Returns a singleton preset of a given type.
     *
     * @param  string $class
     * @param  array|null $options
     * @return PresetInterface|null
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    public function getSingletonPreset(string $class, ?array $options = null) : ?PresetInterface
    {
        if (is_null($options)) {
            if (($this->presetsByClasses[$class] ?? null) === null) {
                $preset = $this->createSingletonPreset($class, $options);
                $this->presetsByClasses[$class] = $preset;
            }
            return $this->presetsByClasses[$class];
        } else {
            return $this->createSingletonPreset($class, $options);
        }
    }

    /**
     * Creates a singleton preset object.
     *
     * @param  string $class
     * @param  string|array $options
     * @return PresetInterface|null
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    protected function createSingletonPreset(string $class, ?array $options = null) : ?PresetInterface
    {
        if (is_null($options)) {
            $options = $this->getSingletonPresetOptionsOrFail($class);
        }
        return $this->createPresetWithOptions($class, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
