<?php
/**
 * @file src/Cruftman/Support/Preset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support;

use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Support\Traits\HasPresetsAggregate;
use Cruftman\Support\Exceptions\PresetException;
use Cruftman\Support\Exceptions\OptionNotFoundException;

/**
 * Base class for <a href="PresetInterface.html">presets</a>.
 */
class Preset implements PresetInterface
{
    use HasTemplateOptions,
        HasPresetsAggregate;

    /**
     * Initializes the object.
     *
     * @param  array $options
     * @param  PresetsAggregateInterface $presetsAggregate
     */
    public function __construct(array $options, PresetsAggregateInterface $presetsAggregate = null)
    {
        $this->setOptions($options);
        $this->setPresetsAggregate($presetsAggregate);
    }

    /**
     * Returns a **preset** that is pointed to by this preset's configuration option.
     *
     * @param string $class type of the target preset,
     * @param string $key name of option that holds the name of the target preset.
     * @return PresetInterface|null
     * @throws PresetException
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

    /**
     * Returns an array of **presets** whose names are stored in this preset's configuration option.
     *
     * @param string $class type of the target presets,
     * @param string $key name of option that holds the array of names of the target presets.
     * @return array|null
     * @throws PresetException
     */
    protected function getRelatedPresetsArray(string $class, string $key, ?array $default = null) : ?array
    {
        if (($optionsArray = $this->getOption($key)) === null) {
            return $default;
        }
        $service = $this->getPresetsAggregate();
        return array_map(function ($options) use ($class, $service) {
            return $service->getNamedPreset($class, $options);
        }, $optionsArray);
    }

    /**
     * Returns an array of **presets** whose names are stored in this preset's configuration option.
     *
     * @param string $class type of the target presets,
     * @param string $key name of option that holds the array of names of the target presets.
     * @return PresetInterface|null
     * @throws PresetException
     * @throws OptionNotFoundException
     */
    protected function getRelatedPresetsArrayOrFail(string $class, string $key) : array
    {
        $optionsArray = $this->getOptionOrFail($key);
        $service = $this->getPresetsAggregate();
        return array_map(function ($options) use ($class, $service) {
            return $service->getNamedPreset($class, $options);
        }, $optionsArray);
    }
}

// vim: syntax=php sw=4 ts=4 et:
