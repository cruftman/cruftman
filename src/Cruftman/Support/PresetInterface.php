<?php
/**
 * @file src/Cruftman/Support/PresetInterface.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support;

use Cruftman\Support\TemplateOptionsInterface;

/**
 * Interface for **preset** objects.
 *
 * A **preset** object represents a part of larger configuration array. Multiple
 * fragments of a single configuration array are represented by multiple
 * **preset** objects maintained by a single
 * <a href="PresetsAggregateInterface.html">preset aggregate</a>.
 * In addition to *options* encapsulated in **preset**, the object also
 * provides a reference to its **aggregate**.
 */
interface PresetInterface extends TemplateOptionsInterface
{
    /**
     * Assigns new PresetsAggregateInterface (or null) to this object.
     *
     * @param PresetsAggregateInterface|null $presetsAggregate
     * @return object $this
     */
    public function setPresetsAggregate(?PresetsAggregateInterface $presetsAggregate);

    /**
     * Returns the aggregate containing this presset.
     *
     * @return PresetsAggregateInterface|null
     */
    public function getPresetsAggregate() : ?PresetsAggregateInterface;
}

// vim: syntax=php sw=4 ts=4 et:
