<?php
/**
 * @file src/Cruftman/Ldap/Service.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Cruftman\Support\PresetsAggregateInterface;
use Cruftman\Support\Traits\HasPresetsAggregate;
use Cruftman\Ldap\Presets\Aggregate;

/**
 * Cruftman's LDAP service.
 */
final class Service
{
    use HasPresetsAggregate;

    /**
     * Creates new service instance using configuration array.
     *
     * @return Service
     */
    public static function createWithConfig(array $config = [])
    {
        return new self(new Aggregate($config));
    }

    /**
     * Initializes the service object.
     *
     * @param array $options
     */
    public function __construct(?PresetsAggregateInterface $presets = null)
    {
        $this->setPresetsAggregate($presets);
    }

    /**
     * Same as getPresetsAggregate().
     *
     * @return PresetsAggregateInterface|null
     */
    public function getPresets() : ?PresetsAggregateInterface
    {
        return $this->getPresetsAggregate();
    }
}

// vim: syntax=php sw=4 ts=4 et:
