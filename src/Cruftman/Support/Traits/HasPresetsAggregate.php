<?php
/**
 * @file src/Cruftman/Support/Traits/HasPresetsAggregate.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support\Traits;

use Cruftman\Support\Preset\AggregateInterface;

/**
 * Add a protected attribute named *$presetsAggregate* and public accessors.
 */
trait HasPresetsAggregate
{
    /**
     * @var \Cruftman\Support\AggregateInterface
     */
    protected $presetsAggregate;

    /**
     * Sets $presetsAggregate to the object.
     *
     * @param  AggregateInterface $presetsAggregate
     * @return object $this
     */
    public function setPresetsAggregate(AggregateInterface $presetsAggregate)
    {
        $this->presetsAggregate = $presetsAggregate;
        return $this;
    }

    /**
     * Returns the $presetsAggregate.
     *
     * @return AggregateInterface|null
     */
    public function getPresetsAggregate() : ?AggregateInterface
    {
        return $this->presetsAggregate;
    }
}

// vim: syntax=php sw=4 ts=4 et:
