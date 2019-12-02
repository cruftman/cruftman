<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasPresets.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Support\OptionsInterface;
use Cruftman\Support\Traits\HasOptions;
use Cruftman\Ldap\Exceptions\PresetException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Ldap presets aggregate.
 */
trait HasPresets
{
    abstract function getOption(string $name);
    abstract function getOptionOrFail(string $name);
    abstract function getPresetKeysByClasses() : array;
    abstract function isSingletonPreset(string $class) : ?bool;
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
     * Configure $resolver to check validity of the preset-related $options
     * provided to __construct().
     *
     * @param  OptionsResolver $resolver
     */
    protected function configurePresetOptionsResolver(OptionsResolver $resolver)
    {
        $options = array_unique(array_values($this->getPresetKeysByClasses()));
        $resolver->setDefined($options);
        foreach ($options as $option) {
            $resolver->setAllowedTypes($option, 'array[]');
            $resolver->setAllowedValues($option, function ($array) {
                return $this->allKeysAreValidOptionKeys($array);
            });
        }
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
            throw new PresetException('"'.$class.'" is not a supported ldap preset class');
        }
        return $key;
    }

    /**
     * @todo Write documentation
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
     * @todo Write documentation
     * @return array
     */
    public function getNamedPresetOptionsOrFail(string $class, string $name) : array
    {
        if ($this->isSingletonPreset($class)) {
            throw new PresetException('"'.$class.'" is a singleton ldap preset');
        }
        $key = $this->getPresetOptionsKeyOrFail($class);
        return $this->getOptionOrFail($key.'.'.$name);
    }

    /**
     * @todo Write documentation
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
     * @todo Write documentation
     * @return array
     */
    public function getSingletonPresetOptionsOrFail(string $class) : array
    {
        if ($this->isSingletonPreset($class) === false) {
            throw new PresetException('"'.$class.'" is not a singleton ldap preset');
        }
        $key = $this->getPresetOptionsKeyOrFail($class);
        return $this->getOptionOrFail($key);
    }

    /**
     * Generates an array of presets' names for a given preset type.
     *
     * @param  string $class
     * @return string[]
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
     * @return object
     */
    public function getNamedPreset(string $class, $options)
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
     * @return object
     */
    protected function getNamedPresetByName(string $class, string $name)
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
     * @return object
     */
    protected function createNamedPreset(string $class, $options)
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
     * @return object
     */
    public function getSingletonPreset(string $class, ?array $options = null)
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
     * @return object
     */
    protected function createSingletonPreset(string $class, ?array $options = null)
    {
        if (is_null($options)) {
            $options = $this->getSingletonPresetOptionsOrFail($class);
        }
        return $this->createPresetWithOptions($class, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
