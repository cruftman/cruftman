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
 * Encapsulates instance of LdapInterface, provides all the LdapInterface
 * methods.
 */
trait ProvidesLdapInterface
{
    use HasLdapInterface;

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
    public function isBound() : bool
    {
        return $this->getLdapInterface()->isBound();
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $dn = null, string $password = null)
    {
        $args = @func_get_args();
        return $this->getLdapInterface()->bind(...$args);
    }

    /**
     * {@inheritdoc}
     */
    public function unbind()
    {
        return $this->getLdapInterface()->unbind();
    }

    /**
     * {@inheritdoc}
     */
    public function add(EntryInterface $entry)
    {
        return $this->getLdapInterface()->add($entry);
    }

    /**
     * {@inheritdoc}
     */
    public function update(EntryInterface $entry)
    {
        return $this->getLdapInterface()->update($entry);
    }

    /**
     * {@inheritdoc}
     */
    public function rename(EntryInterface $entry, string $newRdn, bool $deleteOldRdn = true)
    {
        return $this->getLdapInterface()->rename($entry, $newRdn, $deleteOldRdn);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(EntryInterface $entry)
    {
        return $this->getLdapInterface()->delete($entry);
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
