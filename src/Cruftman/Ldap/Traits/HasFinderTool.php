<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasFinderTool.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Tools\Finder;

/**
 * Add a protected attribute named *$finder* and public accessors.
 */
trait HasFinderTool
{
    /**
     * @var Finder
     */
    protected $finder;

    /**
     * Sets Finder tool to the object.
     *
     * @param  Finder|null $finder
     * @return object $this
     */
    public function setFinder(?Finder $finder)
    {
        $this->finder = $finder;
        return $this;
    }

    /**
     * Returns the Finder tool.
     *
     * @return Finder
     */
    public function getFinder() : Finder
    {
        if ($this->finder === null) {
            $this->setFinder(new Finder);
        }
        return $this->finder;
    }
}

// vim: syntax=php sw=4 ts=4 et:
