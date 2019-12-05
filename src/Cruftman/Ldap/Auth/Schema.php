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
use Cruftman\Ldap\Presets\AuthSchema;
use Cruftman\Ldap\Presets\AuthSource;

/**
 * @todo Write documentation
 */
class Schema
{
    use HasAuthSchemaPreset;

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
     *
     * @param  Status $status
     * @param  array $credentials
     * @return bool
     */
    public function attempt(Status $status, array $credentials) : bool
    {
        if ($this->attemptDirectBind($status, $credentials)) {
            return true;
        }
        return $this->attemptIndirectBind($status, $credentials);
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
    protected function attemptDirectBind(Status $status, array $arguments)
    {
        $sources = array_filter($this->getSources(), function ($source) {
            return (($source->getSearchPreset() === null) && ($source->getLocatePreset() === null));
        });
        return $this->attemptDirectBindWithSources($status, $sources, $arguments);
    }

    /**
     * @todo Write documentation.
     *
     * @param  Status $status
     * @param  Source[] $sources
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptDirectBindWithSources(Status $status, array $sources, array $arguments) : bool
    {
        foreach ($sources as $source) {
            if ($this->attemptDirectBindWithSource($status, $source, $arguments)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @todo Write documentation.
     *
     * @param  Status $status
     * @param  Source $source
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptDirectBindWithSource(Status $status, Source $source, array $arguments) : bool
    {
        $attempt = $source->getAttempt();
        if (!$attempt->bind($status, $arguments)) {
            return false;
        }
        return true;
    }

    /**
     * Make an indirect bind attempt by first searching for bind DN and then
     * trying to bind.
     *
     * @param  Status $status
     * @param  array $arguments
     * @return bool
     */
    protected function attemptIndirectBind(Status $status, array $arguments) : bool
    {
        $uuidkey = $this->getAuthSchemaPreset()->substOption('arguments.useruuid', $arguments, 'useruuid');
        if (($arguments[$uuidkey] ?? null) !== null) {
            $entries = $this->locate($arguments);
        } else {
            $entries = $this->search($arguments);
        }

        return $this->attemptIndirectBindEntries($status, $entries, $arguments);
    }

    /**
     * @todo Write documentation.
     *
     * @param  Status $status
     * @param  array $entries
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptIndirectBindEntries(Status $status, array $entries, array $arguments) : bool
    {
        $ambiguous = $this->getAuthSchemaPreset()->getAmbiguous();
        $count = count($entries);

        if ($count === 0) {
            return false;
        } elseif ($count === 1 || $ambiguous === 'first') {
            return $this->attemptBindEntry($status, $entries[0], $arguments);
        } elseif ($ambiguous === 'each') {
            return $this->attemptBindEntries($status, $entries, $arguments);
        } else {
            return false;
        }
    }

    /**
     * Attempt to bind using DNs and connections of the $entries (previously
     * found within our sources).
     *
     * @param  Status $status
     * @param  array $entries
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptBindEntries(Status $status, array $entries, array $arguments = []) : bool
    {
        foreach ($entries as $entry) {
            if ($this->attemptBindEntry($status, $entry, $arguments)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Attempt to bind using $entry's DN and connection preset.
     *
     * @param  Status $status
     * @param  Entry $entry
     * @param  array $arguments
     *
     * @return bool
     */
    protected function attemptBindEntry(Status $status, Entry $entry, array $arguments)
    {
        $connection = $entry->getConnectionPreset();
        $source = $entry->getSource();
        if ($connection === null || $source === null) {
            return false;
        }
        $attempt = $source->getAttempt();
        $arguments = array_merge(['dn' => $entry->getDn()], $arguments);
        if (!$attempt->bind($status, $arguments, $connection)) {
            return false;
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
