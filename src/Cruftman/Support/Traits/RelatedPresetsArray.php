<?php
/**
 * @file src/Cruftman/Support/Traits/RelatedPreset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support\Traits;

use Cruftman\Support\PresetInterface;
use Cruftman\Support\PresetsAggregateInterface;
use Cruftman\Support\Exceptions\PresetException;
use Cruftman\Support\Exceptions\OptionNotFoundException;

/**
 * Provides getRelatedPresetsArray() and getRelatedPresetsArrayOrFail().
 */
trait RelatedPresetsArray
{
    /**
     * Returns the aggregate containing this presset.
     *
     * @return PresetsAggregateInterface|null
     */
    abstract function getPresetsAggregate() : ?PresetsAggregateInterface;

    /**
     * Get a single option.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    abstract function getOption(string $key, $default = null);

    /**
     * Get an option if exists or throw an exception otherwise.
     *
     * @param  string $key
     * @return mixed
     * @throws OptionNotFoundExcpetion
     */
    abstract function getOptionOrFail(string $key);

    /**
     * Returns an array of **presets** whose names are stored in this preset's configuration option.
     *
     * @param string $class type of the target presets,
     * @param string $key name of option that holds the array of names of the target presets.
     * @return array
     * @throws PresetException
     */
    protected function getRelatedPresetsArray(string $class, string $key, array $default = []) : array
    {
        if (($optionsArray = $this->getOption($key)) === null) {
            return $default;
        }
        $presets = $this->getPresetsAggregate();
        return array_map(function ($options) use ($class, $presets) {
            return $presets->getNamedPreset($class, $options);
        }, $optionsArray);
    }

    /**
     * Returns an array of **presets** whose names are stored in this preset's configuration option.
     *
     * @param string $class type of the target presets,
     * @param string $key name of option that holds the array of names of the target presets.
     * @return array
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    protected function getRelatedPresetsArrayOrFail(string $class, string $key) : array
    {
        $optionsArray = $this->getOptionOrFail($key);
        $presets = $this->getPresetsAggregate();
        return array_map(function ($options) use ($class, $presets) {
            return $presets->getNamedPreset($class, $options);
        }, $optionsArray);
    }
}

// vim: syntax=php sw=4 ts=4 et:
