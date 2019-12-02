<?php
/**
 * @file src/Cruftman/Support/PresetAbstract.php
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

/**
 * Abstract base class for <a href="PresetInterface.html">presets</a>.
 */
class PresetAbstract implements PresetInterface
{
    use HasTemplateOptions,
        HasPresetsAggregate;

    /**
     * Initializes the object.
     *
     * @param  PresetsAggregateInterface $presetsAggregate
     * @param  array $options
     */
    public function __construct(PresetsAggregateInterface $presetsAggregate, array $options)
    {
        $this->setPresetsAggregate($presetsAggregate);
        $this->setOptions($options);
    }

    /**
     * @todo Write documentation
     */
    protected function getRelatedPreset(string $class, string $key, $default = null) : ?PresetInterface
    {
        if (($options = $this->getOption($key)) === null) {
            return $default;
        }
        return $this->getPresetsAggregate()->getNamedPreset($class, $options);
    }

    /**
     * @todo Write documentation
     */
    protected function getRelatedPresetOrFail(string $class, string $key) : PresetInterface
    {
        return $this->getPresetsAggregate()->getNamedPreset($class, $this->getOptionOrFail($key));
    }

    /**
     * @todo Write documentation
     */
    protected function getRelatedPresetsArray(string $class, string $key, array $default = []) : array
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
     * @todo Write documentation
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
