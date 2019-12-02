<?php
/**
 * @file src/Cruftman/Ldap/Preset/AbstractPreset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Ldap\Service;
use Cruftman\Ldap\Traits\HasLdapService;

/**
 * Abstract base class for presets.
 */
class AbstractPreset implements PresetInterface
{
    use HasTemplateOptions,
        HasLdapService;

    /**
     * Initializes the Ldap object.
     *
     * @param  Service $ldapService
     * @param  array $options
     */
    public function __construct(Service $ldapService, array $options)
    {
        $this->setLdapService($ldapService);
        $this->setOptions($options);
    }

    /**
     * @todo Write documentation
     */
    protected function getRelatedPreset(string $class, string $key, $default = null)
    {
        if (($options = $this->getOption($key)) === null) {
            return $default;
        }
        return $this->getLdapService()->getNamedPreset($class, $options);
    }

    /**
     * @todo Write documentation
     */
    protected function getRelatedPresetOrFail(string $class, string $key)
    {
        return $this->getLdapService()->getNamedPreset($class, $this->getOptionOrFail($key));
    }

    /**
     * @todo Write documentation
     */
    protected function getRelatedPresetsArray(string $class, string $key, $default = [])
    {
        if (($optionsArray = $this->getOption($key)) === null) {
            return $default;
        }
        $service = $this->getLdapService();
        return array_map(function ($options) use ($class, $service) {
            return $service->getNamedPreset($class, $options);
        }, $optionsArray);
    }

    /**
     * @todo Write documentation
     */
    protected function getRelatedPresetsArrayOrFail(string $class, string $key)
    {
        $optionsArray = $this->getOptionOrFail($key);
        $service = $this->getLdapService();
        return array_map(function ($options) use ($class, $service) {
            return $service->getNamedPreset($class, $options);
        }, $optionsArray);
    }
}

// vim: syntax=php sw=4 ts=4 et:
