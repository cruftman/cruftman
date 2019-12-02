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
use Cruftman\Support\AbstractPreset;
use Cruftman\Support\Traits\ValidatesOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AuthAttempt preset.
 */
class AuthAttempt extends AbstractPreset
{
    use ValidatesOptions;

    /**
     * Configures OptionsResolver for the AuthAttempt Preset.
     *
     * @param  OptionsResolver $resolver
     */
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
     * Returns array of Connection presets as listed in ``'connections'`` option.
     * @return Connection[]|null
     */
    public function getConnections() : ?array
    {
        return $this->getRelatedPresetsArray(Connection::class, 'connections', null);
    }

    /**
     * Returns the Binding Preset as declared in ``'bind'`` option.
     * @return Binding|null
     */
    public function getBinding() : Binding
    {
        return $this->getRelatedPresetOrFail(Binding::class, 'bind');
    }
}

// vim: syntax=php sw=4 ts=4 et:
