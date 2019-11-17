<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasEntry.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Korowai\Lib\Ldap\EntryInterface;

/**
 * Add a protected attribute named *$entry* and public accessors.
 */
trait HasEntry
{
    /**
     * @var EntryInterface
     */
    protected $entry;

    /**
     * Sets $entry to the object.
     *
     * @param  EntryInterface $entry
     * @return object $this
     */
    public function setEntry(EntryInterface $entry)
    {
        $this->entry = $entry;
        return $this;
    }

    /**
     * Returns the $entry.
     *
     * @return EntryInterface|null
     */
    public function getEntry() : ?EntryInterface
    {
        return $this->entry;
    }
}

// vim: syntax=php sw=4 ts=4 et:
