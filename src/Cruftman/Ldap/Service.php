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

use Cruftman\Support\Traits\HasOptions;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Ldap\Preset\AuthRequest;
use Cruftman\Ldap\Preset\AuthSource;
use Cruftman\Ldap\Preset\Binding;
use Cruftman\Ldap\Preset\Connection;
use Cruftman\Ldap\Preset\Ldap;
use Cruftman\Ldap\Preset\SearchQuery;
use Illuminate\Support\Arr;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Cruftman LDAP service.
 *
 * The main purpose of Service object is to maintain object we call Presets.
 * A Preset represents certain part of the "ldap" config. For example,
 * ``config('ldap.connections.xyz')`` is a part of ldap config and provides
 * configuration parameters for ldap connections.
 */
class Service
{
    use HasOptions,
        ValidatesOptions;

    /**
     * @var array
     */
    protected $presets = [
        'connections' => [],
        'bindings' => [],
        'instances' => [],
        'searches' => [],
        'auth_sources' => []
    ];

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
     * {@inheritdoc}
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $options = array_keys($this->presets);
        $resolver->setDefined($options);
        foreach ($options as $option) {
            $resolver->setAllowedTypes($option, 'array[]');
            $resolver->setAllowedValues($option, function ($array) {
                return $this->allKeysAreValidOptionKeys($array);
            });
        }
    }

    /**
     * Returns a list of available connection presets.
     *
     * @return array
     */
    public function connections() : array
    {
        return $this->getPresets('connections');
    }

    /**
     * Returns a list of available binding presets.
     *
     * @return array
     */
    public function bindings() : array
    {
        return $this->getPresets('bindings');
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
        return $this->getPresets('instances');
    }

    /**
     * Returns a list of available search query presets.
     *
     * @return array
     */
    public function searchQueries() : array
    {
        return $this->getPresets('searches');
    }

    /**
     * Returns a list of available authentication source presets.
     *
     * @return array
     */
    public function authSources() : array
    {
        return $this->getPresets('auth_sources');
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
        return $this->getPreset('connections', $name, Connection::class);
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
        return $this->getPreset('bindings', $name, Binding::class);
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
        return $this->getPreset('instances', $name, Ldap::class);
    }

    /**
     * Returns a SearchQuery preset.
     *
     * @param  string $name
     * @return SearchQuery
     */
    public function searchQuery(string $name) : SearchQuery
    {
        return $this->getPreset('searches', $name, SearchQuery::class);
    }

    /**
     * Returns an AuthSource preset.
     *
     * @param  string $name
     * @return AuthSource
     */
    public function authSource(string $name) : AuthSource
    {
        return $this->getPreset('auth_sources', $name, AuthSource::class);
    }

    /**
     * Generates an array of presets' names for a given preset type.
     *
     * @param  string $scope
     * @return array
     */
    protected function getPresets(string $scope) : array
    {
        $optionKeys = array_keys($this->getOption($scope, []));
        $presetKeys = array_keys(Arr::get($this->presets, $scope, []));

        return array_unique(array_merge($optionKeys, $presetKeys));
    }

    /**
     * Returns a named preset of a given type.
     *
     * The preset object gets created when it's requested for the first time.
     *
     * @param  string $scope
     * @param  string $name
     * @param  string $class
     * @return object
     */
    protected function getPreset(string $scope, string $name, string $class)
    {
        $path = $scope.'.'.$name;
        if (Arr::get($this->presets, $path) === null) {
            $preset = $this->createPreset($scope, $name, $class);
            Arr::set($this->presets, $path, $preset);
        }
        return Arr::get($this->presets, $path);
    }

    /**
     * Creates a named preset.
     *
     * @param  string $scope
     * @param  string $name
     * @param  string $class
     * @return object
     */
    protected function createPreset(string $scope, string $name, string $class)
    {
        $options = $this->getOptionOrFail($scope.'.'.$name);
        return new $class($this, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
