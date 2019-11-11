<?php
/**
 * @file src/Cruftman/Ldap/Preset/AuthAttempt.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Korowai\Lib\Ldap\LdapInterface;
use Cruftman\Support\Traits\ValidatesOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AuthAttempt preset.
 */
class AuthAttempt extends AbstractPreset
{
    use ValidatesOptions;

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
         $resolver->setRequired(['bind'])
                  ->setDefined(['connections', 'filter', 'attributes'])
                  ->setAllowedTypes('connections', 'array')
                  ->setAllowedTypes('bind', ['string', 'array'])
                  ->setAllowedTypes('filter', 'string')
                  ->setAllowedTypes('attributes', 'array');
    }

    /**
     * @todo Write documentation
     * @return Connection[]|null
     */
    public function getConnections() : ?array
    {
        if (($connectionsOptions = $this->getOption('connections')) === null) {
            return null;
        }
        return array_map(function ($options) {
            return $this->getLdapService()->getConnection($options);
        }, $connectionsOptions);
    }

    /**
     * @todo Write documentation
     * @return Binding|null
     */
    public function getBinding() : Binding
    {
        return $this->getRelatedPresetOrFail(Binding::class, 'bind');
    }
}

// vim: syntax=php sw=4 ts=4 et:
