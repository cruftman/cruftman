<?php
/**
 * @file src/Cruftman/Ldap/Presets/AuthSource.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Presets;

use Korowai\Lib\Ldap\Adapter\AdapterInterface;
use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Exception\LdapException;
use Cruftman\Support\Preset;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Support\Traits\RelatedPreset;
use Cruftman\Support\Traits\RelatedPresetsArray;
use Cruftman\Ldap\Service;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * @todo Write documentation
 */
class AuthSource extends Preset
{
    use ValidatesOptions,
        RelatedPreset,
        RelatedPresetsArray;

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['attempt'])
                 ->setDefined(['sessions', 'search', 'locate'])
                 ->setAllowedTypes('attempt', ['string', 'array'])
                 ->setAllowedTypes('sessions', 'array')
                 ->setAllowedTypes('search', ['string', 'array'])
                 ->setAllowedTypes('locate', ['string', 'array']);

        $this->setOptionsNormalizers($resolver);
    }

    /**
     * @todo Write documentation.
     * @param  OptionsResolver $resolver
     */
    protected function setOptionsNormalizers(OptionsResolver $resolver)
    {
        foreach (['search', 'locate'] as $key) {
            $this->setSearchOptionNormalizer($resolver, $key);
        }
    }

    /**
     * @todo Write documentation.
     * @param  OptionsResolver $resolver
     * @param  string $key
     */
    protected function setSearchOptionNormalizer(OptionsResolver $resolver, string $key)
    {
        $resolver->setNormalizer($key, function (Options $options, $value) use ($key) {
            if (($options['sessions'] ?? null) === null) {
                $message = 'The required option "sessions" is missing (required by "'.$key.'" option)';
                throw new MissingOptionsException($message);
            }
            return $value;
        });
    }

    /**
     * @todo Write documentation.
     * @return Session[]
     */
    public function getSessions() : array
    {
        return $this->getRelatedPresetsArray(Session::class, 'sessions');
    }

    /**
     * @todo Write documentation
     * @return Search|null
     */
    public function getSearch() : ?Search
    {
        return $this->getRelatedPreset(Search::class, 'search');
    }

    /**
     * @todo Write documentation
     * @return Search|null
     */
    public function getLocate() : ?Search
    {
        return $this->getRelatedPreset(Search::class, 'locate');
    }

    /**
     * @todo Write documentation
     * @return AuthAttempt
     */
    public function getAuthAttempt() : AuthAttempt
    {
        return $this->getRelatedPreset(AuthAttempt::class, 'attempt');
    }
}

// vim: syntax=php sw=4 ts=4 et:
