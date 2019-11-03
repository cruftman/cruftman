<?php
/**
 * @file src/Cruftman/Ldap/Traits/Resiliency.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Korowai\Lib\Ldap\Exception\LdapException;
use Cruftman\Ldap\Preset\ResiliencyInterface;
use Cruftman\Ldap\Preset\PresetInterface;

/**
 * Extends the HasLdapInterface trait, adding all the LdapInterface methods.
 */
trait Resiliency
{
    protected function configureResiliencyOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setDefined(['retry', 'timeout', 'failover'])
                 ->setAllowedTypes('retry', 'int')
                 ->setAllowedTypes('timeout', ['string', 'number'])
                 ->setAllowedTypes('failover', ['string', 'string[]']);
    }

    /**
     * @todo Write documentation
     * @return string[]
     */
    protected function getFailoverPresetNames() : array
    {
        $names = $this->getOption('failover', []);
        if (is_string($names)) {
            $names = [$names];
        }
        return $names;
    }

    /**
     * @todo Write documentation.
     *
     * @return PresetInterface[]|null
     */
    public function getFailoverPresets() : array
    {
        $names = $this->getFailverPresetNames();
        return array_map(function ($name) {
            return $this->getSiblingPreset($name);
        }, $names);
    }
}

// vim: syntax=php sw=4 ts=4 et:
