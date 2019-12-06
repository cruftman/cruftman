<?php
/**
 * @file src/Cruftman/Support/Preset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support;

use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Support\Traits\HasPresetsAggregate;
use Cruftman\Support\Exceptions\PresetException;
use Cruftman\Support\Exceptions\OptionNotFoundException;

/**
 * Base class for <a href="PresetInterface.html">presets</a>.
 */
class Preset implements PresetInterface
{
    use HasTemplateOptions,
        HasPresetsAggregate;

    /**
     * Initializes the object.
     *
     * @param  array $options
     * @param  PresetsAggregateInterface $presetsAggregate
     */
    public function __construct(array $options, PresetsAggregateInterface $presetsAggregate = null)
    {
        $this->setOptions($options);
        $this->setPresetsAggregate($presetsAggregate);
    }
}

// vim: syntax=php sw=4 ts=4 et:
