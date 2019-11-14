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

namespace Cruftman\Ldap\Auth;

use Cruftman\Ldap\Traits\HasAuthSchemaPreset;
use Cruftman\Ldap\Preset\AuthSchema;
use Cruftman\Ldap\Preset\AuthSource;

/**
 * @todo Write documentation
 */
class Schema
{
    use HasAuthSchemaPreset;

    /**
     * @var AttemptState
     */
    protected $attemptState = null;

    /**
     * @var Entry
     */
    protected $attemptEntry = null;

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
     * Get the array of Source objects related to this Schema.
     *
     * @return Source[]
     */
    public function getSources() : array
    {
        if (!isset($this->sources)) {
            $this->sources = $this->createSources();
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
        return array_map(function ($preset) {
            return new Source($preset);
        }, $this->getAuthSourcesPresets());
    }

    /**
     * Returns the array of nested AuthSource presets.
     *
     * @return AuthSource[]
     */
    public function getAuthSourcesPresets() : array
    {
        return $this->getAuthSchemaPreset()->getSources();
    }

    /**
     * @todo Write documentation.
     * @return string
     */
    public function getAmbiguousOption() : string
    {
        return $this->getAuthSchemaPreset()->getAmbiguous();
    }

    /**
     * Returns the AttemptState object.
     *
     * @return AttemptState|null
     */
    public function getAttemptState() : ?AttemptState
    {
        return $this->attemptState;
    }

    /**
     * Returns the Entry used by attempt().
     *
     * @return Entry|null
     */
    public function getAttemptEntry() : ?Entry
    {
        return $this->attemptEntry;
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $credentials
     * @return bool
     */
    public function attempt(array $credentials = []) : bool
    {
        $this->attemptState = null;
        $this->attemptEntry = null;
        if ($this->attemptDirectBind($credentials)) {
            return true;
        }
        return $this->attemptIndirectBind($credentials);
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $arguments
     * @return Entry[]
     */
    public function search(array $arguments = [])
    {
        return $this->findWithSources($this->getSources(), 'search', $arguments);
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $arguments
     * @return Entry[]
     */
    public function locate(array $arguments = [])
    {
        return $this->findWithSources($this->getSources(), 'locate', $arguments);
    }

    /**
     * @todo Write documentation.
     */
    protected function attemptDirectBind(array $arguments = [])
    {
        $sources = array_filter($this->getSources(), function ($source) {
            return (($source->getSearchPreset() === null) && ($source->getLocatePreset() === null));
        });
        return $this->attemptDirectBindWithSources($sources, $arguments);
    }

    /**
     * @todo Write documentation.
     * @param  Source[] $sources
     * @param  array $arguments
     * @return bool
     */
    protected function attemptDirectBindWithSources(array $sources, array $arguments = []) : bool
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
     * @param  Source $source
     * @param  array $arguments
     * @return bool
     */
    protected function attemptDirectBindWithSource(Source $source, array $arguments = []) : bool
    {
        $attempt = $source->getAttempt();
        if (!$attempt->bind($arguments)) {
            return false;
        }
        $this->attemptState = $attempt->getState()->setSource($source);
        return true;
    }

    /**
     * Make an indirect bind attempt by first searching for bind DN and then
     * trying to bind.
     *
     * @param  array $arguments
     * @return bool
     */
    protected function attemptIndirectBind(array $arguments = []) : bool
    {
        $uuidkey = $this->getAuthSchema()->substOption('arguments.useruuid', $arguments, 'useruuid');
        if (($arguments[$uuidkey] ?? null) !== null) {
            $entries = $this->locate($arguments);
        } else {
            $entries = $this->search($arguments);
        }

        return $this->attemptIndirectBindEntries($entries, $arguments);
    }

    /**
     * @todo Write documentation.
     * @param  Entry[] $entries
     * @param  array $arguments
     * @return bool
     */
    protected function attemptIndirectBindEntries(array $entries, array $arguments = []) : bool
    {
        $ambiguous = $this->getAmbiguousOption();
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
     * @param  Entry[] $entries
     * @param  array $arguments
     * @return bool
     */
    protected function attemptBindEntries(array $entries, array $arguments = []) : bool
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
     * @return bool
     */
    protected function attemptBindEntry(Entry $entry, array $arguments = [])
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
        $this->attemptState = $attempt->getState()->setSource($source);
        $this->attemptEntry = $entry;
        return true;
    }

    /**
     * @todo Write documentation.
     *
     * @param  Source[] $sources
     * @param  string $method
     * @param  array $arguments
     * @return array
     */
    protected function findWithSources(array $sources, string $method, array $arguments = [])
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
