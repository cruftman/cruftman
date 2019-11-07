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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @todo Write documentation
 */
trait HasPresets
{
    abstract function getOption(string $name);
    abstract function getOptionOrFail(string $name);
    abstract function getPresetKeysByClasses() : array;

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
     * @throws \Exception
     */
    public function getPresetOptionsKeyOrFail(string $class) : string
    {
        if (($key = $this->getPresetOptionsKey($class)) === null) {
            // FIXME: specialized exception
            throw new \Exception('unsupported Preset class "'.$class.'"');
        }
        return $key;
    }

    /**
     * @todo Write documentation
     * @return array|null
     */
    public function getPresetOptions(string $class, string $name) : ?array
    {
        $key = $this->getPresetOptionsKey($class);
        return $key === null ? null : $this->getOptions($key.'.'.$name);
    }

    /**
     * @todo Write documentation
     * @return array
     */
    public function getPresetOptionsOrFail(string $class, string $name) : array
    {
        $key = $this->getPresetOptionsKeyOrFail($class);
        return $this->getOptionOrFail($key.'.'.$name);
    }

    /**
     * Generates an array of presets' names for a given preset type.
     *
     * @param  string $class
     * @return string[]
     */
    public function getPresets(string $class) : array
    {
        $key = $this->getPresetOptionsKeyOrFail($class);
        $optionKeys = array_keys($this->getOption($key, []));
        $presetKeys = array_keys($this->pPresetsByClasses[$class] ?? []);

        return array_unique(array_merge($optionKeys, $presetKeys));
    }

    /**
     * Returns a preset of a given type.
     *
     * @param  string $class
     * @param  string|array $options
     * @return object
     */
    public function getPreset(string $class, $options)
    {
        if (is_string($options)) {
            return $this->getPresetByName($class, $options);
        } else {
            return $this->createPreset($class, $options);
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
    protected function getPresetByName(string $class, string $name)
    {
        if (($this->presetsByClasses[$class][$name] ?? null) === null) {
            $preset = $this->createPreset($class, $name);
            $this->presetsByClasses[$class][$name] = $preset;
        }
        return $this->presetsByClasses[$class][$name];
    }

    /**
     * Creates a preset object.
     *
     * @param  string $class
     * @param  string|array $options
     * @return object
     */
    protected function createPreset(string $class, $options)
    {
        if (is_string($options)) {
            $options = $this->getPresetOptionsOrFail($class, $options);
        }
        return new $class($this, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
