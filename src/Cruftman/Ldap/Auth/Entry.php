<?php
/**
 * @file src/Cruftman/Ldap/Auth/Entry.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Auth;

use Korowai\Lib\Ldap\EntryInterface;
use Cruftman\Ldap\Traits\HasConnectionPreset;
use Cruftman\Ldap\Traits\HasEntry;
use Cruftman\Ldap\Traits\DecoratesEntryInterface;

/**
 * @todo Write documentation.
 */
class Entry implements EntryInterface
{
    use HasConnectionPreset,
        HasEntry,
        DecoratesEntryInterface;

    /**
     * @var Source
     */
    protected $source = null;

    /**
     * Initializes the object.
     *
     * @param  EntryInterface $entry
     */
    public function __construct(EntryInterface $entry)
    {
        $this->setEntry($entry);
    }

    /**
     * Return the Source object used to find the entry.
     * @return Source|null
     */
    public function getSource() : ?Source
    {
        return $this->source;
    }

    /**
     * Set the Source object.
     *
     * @return Entry $this
     */
    public function setSource(?Source $source) : Entry
    {
        $this->source = $source;
        return $this;
    }
}

// vim: syntax=php sw=4 ts=4 et:
