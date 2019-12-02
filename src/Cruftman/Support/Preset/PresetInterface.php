<?php
/**
 * @file src/Cruftman/Support/Preset/PresetInterface.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support\Preset;

use Cruftman\Support\TemplateOptionsInterface;

/**
 * Interface for **preset** objects.
 *
 * A **preset** object represents a part of larger configuration array. Multiple
 * fragments of a single configuration array are represented by multiple
 * **preset** objects maintained by a single
 * <a href="AggregateInterface.html">preset aggregate</a>.
 * In addition to *options* encapsulated in **preset**, the object also
 * provides a reference to its **aggregate**.
 */
interface PresetInterface extends TemplateOptionsInterface
{
    /**
     * Returns the aggregate containing this presset.
     *
     * @return AggregateInterface|null
     */
    public function getPresetsAggregate() : ?AggregateInterface;
}

// vim: syntax=php sw=4 ts=4 et:
