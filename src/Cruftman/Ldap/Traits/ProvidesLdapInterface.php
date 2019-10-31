<?php
/**
 * @file src/Cruftman/Ldap/Traits/ProvidesLdapInterface.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\EntryInterface;
use Korowai\Lib\Ldap\Adapter\AdapterInterface;
use Korowai\Lib\Ldap\Adapter\BindingInterface;
use Korowai\Lib\Ldap\Adapter\EntryManagerInterface;
use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\CompareQueryInterface;

/**
 * @todo Write documentation.
 */
trait ProvidesLdapInterface
{
    /**
     * @var LdapInterface
     */
    protected $ldapInterface = null;


    /**
     * Return ldap interface.
     *
     * @return LdapInterface
     */
    public function getLdapInterface() : LdapInterface
    {
        if (!isset($this->ldapInterface) && method_exists($this, 'createLdapInterface')) {
            $this->ldapInterface = $this->createLdapInterface();
        }
        return $this->ldapInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter() : AdapterInterface
    {
        return $this->getLdapInterface()->getAdapter();
    }

    /**
     * {@inheritdoc}
     */
    public function isBound() : bool
    {
        return $this->getLdapInterface()->isBound();
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $dn = null, string $password = null)
    {
        return $this->getLdapInterface()->bind($dn, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function unbind()
    {
        $this->getLdapInterface()->unbind();
    }

    /**
     * {@inheritdoc}
     */
    public function add(EntryInterface $entry)
    {
        $this->getLdapInterface()->add($entry);
    }

    /**
     * {@inheritdoc}
     */
    public function update(EntryInterface $entry)
    {
        $this->getLdapInterface()->update($entry);
    }

    /**
     * {@inheritdoc}
     */
    public function rename(EntryInterface $entry, string $newRdn, bool $deleteOldRdn = true)
    {
        $this->getLdapInterface()->rename($entry, $newRdn, $deleteOldRdn);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(EntryInterface $entry)
    {
        $this->getLdapInterface()->delete($entry);
    }

    /**
     * {@inheritdoc}
     */
    public function getBinding() : BindingInterface
    {
        return $this->getLdapInterface()->getBinding();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntryManager() : EntryManagerInterface
    {
        return $this->getLdapInterface()->getEntryManager();
    }

    /**
     * {@inheritdoc}
     */
    public function createSearchQuery(string $base_dn, string $filter, array $options = array()) : SearchQueryInterface
    {
        return $this->getLdapInterface()->createSearchQuery($base_dn, $filter, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function createCompareQuery(string $dn, string $attribute, string $value) : CompareQueryInterface
    {
        return $this->getLdapInterface()->createCompareQuery($dn, $attribute, $value);
    }
}

// vim: syntax=php sw=4 ts=4 et:
