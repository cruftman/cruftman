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
use Cruftman\Ldap\Traits\HasAuthSourcePreset;
use Cruftman\Ldap\Traits\DecoratesEntryInterface;

/**
 * @todo Write documentation.
 */
class Entry implements EntryInterface
{
    use HasConnectionPreset,
        HasAuthSourcePreset,
        DecoratesEntryInterface;

    /**
     * Initializes the object.
     *
     * @param  EntryInterface $entry
     */
    public function __construct(EntryInterface $entry)
    {
        $this->setEntry($entry);
    }
}

// vim: syntax=php sw=4 ts=4 et:
