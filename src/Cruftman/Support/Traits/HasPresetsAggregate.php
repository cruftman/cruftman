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

use Cruftman\Support\PresetInterface;
use Cruftman\Support\PresetsAggregateInterface;

/**
 * Add a protected attribute named *$presetsAggregate* and public accessors.
 */
trait HasPresetsAggregate
{
    /**
     * @var PresetsAggregateInterface
     */
    protected $presetsAggregate;

    /**
     * Sets $presetsAggregate to the object.
     *
     * @param  PresetsAggregateInterface $presetsAggregate
     * @return object $this
     */
    public function setPresetsAggregate(?PresetsAggregateInterface $presetsAggregate)
    {
        $this->presetsAggregate = $presetsAggregate;
        return $this;
    }

    /**
     * Returns the $presetsAggregate.
     *
     * @return PresetsAggregateInterface|null
     */
    public function getPresetsAggregate() : ?PresetsAggregateInterface
    {
        return $this->presetsAggregate;
    }
}

// vim: syntax=php sw=4 ts=4 et:
