<?php
/**
 * @file src/Cruftman/Ldap/Auth/Schema.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Auth;

use Cruftman\Ldap\Traits\HasAuthSchemaPreset;
use Cruftman\Ldap\Traits\HasAuthStatus;
use Cruftman\Ldap\Traits\HasConnectorTool;
use Cruftman\Ldap\Traits\HasBinderTool;
use Cruftman\Ldap\Traits\HasFinderTool;
use Cruftman\Ldap\Presets\AuthSchema;
use Cruftman\Ldap\Presets\AuthSource;

use Cruftman\Ldap\Tools\Connector;
use Cruftman\Ldap\Tools\Finder;
use Cruftman\Ldap\Tools\Binder;

/**
 * Drives authentication against multiple ldap auth sources.
 */
class Schema
{
    use HasAuthSchemaPreset,
        HasAuthStatus,
        HasConnectorTool,
        HasBinderTool,
        HasFinderTool;

    /**
     * @var Source[]
     */
    protected $sources = null;

    /**
     * Initializes the Auth object.
     *
     * @param  AuthSchema $preset
     */
    public function __construct(AuthSchema $preset)
    {
        $this->setAuthSchemaPreset($preset);
    }

    /**
     * Assings auth Sources to this object.
     *
     * @param  array|null $sources
     * @return Schema $this
     */
    public function setSources(?array $sources)
    {
        $this->sources = $sources;
        return $this;
    }

    /**
     * Get the array of Source objects related to this Schema.
     *
     * @return Source[]
     */
    public function getSources() : array
    {
        if (!isset($this->sources)) {
            $this->setSources($this->createSources());
        }
        return $this->sources;
    }

    /**
     * Creates fresh array of Source objects.
     *
     * @return Source[]
     */
    protected function createSources() : array
    {
        $connector = $this->getConnector();
        $binder = $this->getBinder();
        $finder = $this->getFinder();
        return array_map(function ($preset) use ($connector, $binder, $finder) {
            $source = new Source($preset);
            $source->setConnector($connector)
                   ->setBinder($binder)
                   ->setFinder($finder);
            return $source;
        }, $this->getAuthSchemaPreset()->sources());
    }

    /**
     * Try to authenticate user with given *$credentials*.
     *
     * @param  array $credentials
     * @return bool
     */
    public function attempt(array $credentials) : bool
    {
        if ($this->attemptDirectBind($credentials)) {
            return true;
        }
        return $this->attemptIndirectBind($credentials);
    }

    /**
     * Search user (by username or such) within auth sources.
     *
     * @param  array $arguments
     * @return Entry[]
     */
    public function search(array $arguments) : array
    {
        return $this->findWithSources($this->getSources(), 'search', $arguments);
    }

    /**
     * Search user (by useruuid or such) within auth sources.
     *
     * @param  array $arguments
     * @return Entry[]
     */
    public function locate(array $arguments) : array
    {
        return $this->findWithSources($this->getSources(), 'locate', $arguments);
    }

    /**
     * Attempt to bind user against all sources that provide neither *search*
     * nor *locate* presets.
     *
     * @param  array $arguments
     * @return bool
     */
    protected function attemptDirectBind(array $arguments) : bool
    {
        $sources = $this->getSourcesWithoutSearchPresets();
        return $this->attemptDirectBindWithSources($sources, $arguments);
    }

    /**
     * Returns auth sources that have neither *search* nor *locate* presets.
     *
     * @return array
     */
    protected function getSourcesWithoutSearchPresets() : array
    {
        return array_filter($this->getSources(), function ($source) {
            $preset = $source->getAuthSourcePreset();
            $search = $preset ? $preset->search() : null;
            $locate = $preset ? $preset->locate() : null;
            return (($search === null) && ($locate === null));
        });
    }

    /**
     * @todo Write documentation.
     *
     * @param  Source[] $sources
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptDirectBindWithSources(array $sources, array $arguments) : bool
    {
        foreach ($sources as $source) {
            if ($this->attemptDirectBindWithSource($source, $arguments)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @todo Write documentation.
     *
     * @param  Source $source
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptDirectBindWithSource(Source $source, array $arguments) : bool
    {
        $attempt = $source->getAttempt();
        if (!$attempt->bind($arguments)) {
            return false;
        }
        $this->setAuthStatus($status = $attempt->getAuthStatus());
        if (($entry = $status->getBindEntry()) !== null) {
            $entry->setSource($source);
        }
        return true;
    }

    /**
     * Make an indirect bind attempt by first searching for bind DN and then
     * trying to bind.
     *
     * @param  array $arguments
     * @return bool
     */
    protected function attemptIndirectBind(array $arguments) : bool
    {
        $uuidkey = $this->getAuthSchemaPreset()->substOption('arguments.useruuid', $arguments, 'useruuid');
        if (($arguments[$uuidkey] ?? null) !== null) {
            $entries = $this->locate($arguments);
        } else {
            $entries = $this->search($arguments);
        }

        return $this->attemptIndirectBindEntries($entries, $arguments);
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $entries
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptIndirectBindEntries(array $entries, array $arguments) : bool
    {
        $ambiguous = $this->getAuthSchemaPreset()->ambiguous();
        $count = count($entries);

        if ($count === 0) {
            return false;
        } elseif ($count === 1 || $ambiguous === 'first') {
            return $this->attemptBindEntry($entries[0], $arguments);
        } elseif ($ambiguous === 'each') {
            return $this->attemptBindEntries($entries, $arguments);
        } else {
            return false;
        }
    }

    /**
     * Attempt to bind using DNs and connections of the $entries (previously
     * found within our sources).
     *
     * @param  array $entries
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptBindEntries(array $entries, array $arguments) : bool
    {
        foreach ($entries as $entry) {
            if ($this->attemptBindEntry($entry, $arguments)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Attempt to bind using $entry's DN and connection preset.
     *
     * @param  Entry $entry
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptBindEntry(Entry $entry, array $arguments)
    {
        $connection = $entry->getConnectionPreset();
        $source = $entry->getSource();
        if ($connection === null || $source === null) {
            return false;
        }
        $attempt = $source->getAttempt();
        $arguments = array_merge(['dn' => $entry->getDn()], $arguments);
        if (!$attempt->bind($arguments, $connection)) {
            return false;
        }
        $this->setAuthStatus($attempt->getAuthStatus());
        if ($this->getAuthStatus()->getBindEntry() === null) {
            $this->getAuthStatus()->setBindEntry($entry);
        }
        return true;
    }

    /**
     * Find user in authentication sources.
     *
     * @param  array $sources
     * @param  string $method
     * @param  array $arguments
     *
     * @return Entry[]
     */
    protected function findWithSources(array $sources, string $method, array $arguments)
    {
        $entries = [];
        foreach ($sources as $source) {
            $found = call_user_func([$source, $method], $arguments);
            $entries = array_merge($entries, $found);
        }
        return $entries;
    }
}

// vim: syntax=php sw=4 ts=4 et:
