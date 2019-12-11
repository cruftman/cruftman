<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasBinderTool.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Tools\Binder;

/**
 * Add a protected attribute named *$binder* and public accessors.
 */
trait HasBinderTool
{
    /**
     * @var Binder
     */
    protected $binder;

    /**
     * Sets Binder tool to the object.
     *
     * @param  Binder|null $binder
     * @return object $this
     */
    public function setBinder(?Binder $binder)
    {
        $this->binder = $binder;
        return $this;
    }

    /**
     * Returns the Binder tool.
     *
     * @return Binder
     */
    public function getBinder() : Binder
    {
        if ($this->binder === null) {
            $this->setBinder(new Binder);
        }
        return $this->binder;
    }
}

// vim: syntax=php sw=4 ts=4 et:
