<?php
/**
 * @file src/Cruftman/Ldap/Traits/DecoratesEntryInterface.php
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
trait DecoratesEntryInterface
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

    /**
     * {@inheritdoc}
     */
    public function getDn() : string
    {
        return $this->getEntry()->getDn();
    }

    /**
     * {@inheritdoc}
     */
    public function setDn(string $dn)
    {
        return $this->getEntry()->setDn($dn);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes() : array
    {
        return $this->getEntry()->getAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(string $name) : array
    {
        return $this->getEntry()->getAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute(string $name) : bool
    {
        return $this->getEntry()->hasAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        return $this->getEntry()->setAttributes($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute(string $name, array $values)
    {
        return $this->getEntry()->setAttribute($name, $values);
    }
}

// vim: syntax=php sw=4 ts=4 et:
