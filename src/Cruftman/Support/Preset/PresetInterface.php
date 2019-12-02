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
 * Interface provided by a Preset.
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
