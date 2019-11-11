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
use Korowai\Lib\Ldap\LdapInterface;
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
     * @var LdapInterface
     */
    protected $userLdap;

    /**
     * @var bool
     */
    protected $authResult = false;

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
     * @todo Write documentation.
     * @param  LdapInterface|null $ldap
     * @return Entry $this
     */
    public function setUserLdap(?LdapInterface $ldap)
    {
        $this->userLdap = $ldap;
        return $this;
    }

    /**
     * @todo Write documentation.
     * @return LdapInterface|null
     */
    public function getUserLdap() : ?LdapInterface
    {
        return $this->userLdap;
    }


    /**
     * @todo Write documentation.
     * @param  bool $authResult
     * @return Entry $this
     */
    public function setAuthResult(bool $authResult)
    {
        $this->authResult = $authResult;
        return $this;
    }

    /**
     * @todo Write documentation.
     * @return bool
     */
    public function getAuthResult() : bool
    {
        return $this->authResult;
    }
}

// vim: syntax=php sw=4 ts=4 et:
