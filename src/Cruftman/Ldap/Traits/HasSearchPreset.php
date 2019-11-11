<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasSearchPreset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Preset\Search;

/**
 * Add a protected attribute named *$searchPreset* and public accessors.
 */
trait HasSearchPreset
{
    /**
     * @var Search
     */
    protected $searchPreset;

    /**
     * Sets Search preset to the object.
     *
     * @param  Search $searchPreset
     * @return object $this
     */
    public function setSearchPreset(Search $searchPreset)
    {
        $this->searchPreset = $searchPreset;
        return $this;
    }

    /**
     * Returns the Search preset.
     *
     * @return Search|null
     */
    public function getSearchPreset() : ?Search
    {
        return $this->searchPreset;
    }
}

// vim: syntax=php sw=4 ts=4 et:
