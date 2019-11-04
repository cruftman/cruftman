<?php
/**
 * @file src/Cruftman/Ldap/Preset/AuthSource.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Korowai\Lib\Ldap\Exception\LdapException;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Ldap\Service;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @todo Write documentation
 */
class AuthSource extends AbstractPreset
{
    use ValidatesOptions;

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['attempt'])
                 ->setDefined(['sessions', 'search'])
                 ->setAllowedTypes('sessions', 'array')
                 ->setAllowedTypes('search', ['string', 'array'])
                 ->setDefault('attempt', function (OptionsResolver $nested) {
                     $nested->setRequired(['connections', 'bind'])
                            ->setAllowedTypes('connections', 'array')
                            ->setAllowedTypes('bind', ['string', 'array']);
                 });
    }
}

// vim: syntax=php sw=4 ts=4 et:
