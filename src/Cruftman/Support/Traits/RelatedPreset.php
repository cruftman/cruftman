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
 * Provides getRelatedPreset() and getRelatedPresetOrFail().
 */
trait RelatedPreset
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
     * Returns a **preset** that is pointed to by this preset's configuration option.
     *
     * @param string $class type of the target preset,
     * @param string $key name of option that holds the name of the target preset.
     * @return PresetInterface|null
     */
    protected function getRelatedPreset(string $class, string $key, $default = null) : ?PresetInterface
    {
        if (($options = $this->getOption($key)) === null) {
            return $default;
        }
        return $this->getPresetsAggregate()->getNamedPreset($class, $options);
    }

    /**
     * Returns a **preset** that is pointed to by this preset's configuration option.
     *
     * @param string $class type of the target preset,
     * @param string $key name of option that holds the name of the target preset.
     * @return PresetInterface
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    protected function getRelatedPresetOrFail(string $class, string $key) : PresetInterface
    {
        return $this->getPresetsAggregate()->getNamedPreset($class, $this->getOptionOrFail($key));
    }
}

// vim: syntax=php sw=4 ts=4 et:
