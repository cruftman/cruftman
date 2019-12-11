<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasAuthStatus.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Auth\Status;

/**
 * Add a protected attribute named *$authStatus* and public accessors.
 */
trait HasAuthStatus
{
    /**
     * @var Status
     */
    protected $authStatus = null;

    /**
     * Sets auth Status to the object.
     *
     * @param  Status|null $authStatus
     * @return object $this
     */
    public function setAuthStatus(?Status $status)
    {
        $this->authStatus = $status;
        return $this;
    }

    /**
     * Returns the auth Status.
     *
     * @return Status
     */
    public function getAuthStatus() : Status
    {
        if ($this->authStatus === null) {
            $this->setAuthStatus(new Status);
        }
        return $this->authStatus;
    }
}

// vim: syntax=php sw=4 ts=4 et:
