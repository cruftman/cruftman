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

use Cruftman\Support\Preset;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Support\Traits\RelatedPreset;
use Cruftman\Support\Traits\RelatedPresetsArray;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * A *preset* object that represents single ldap data source for
 * authentication.
 */
class AuthSource extends Preset
{
    use ValidatesOptions,
        RelatedPreset,
        RelatedPresetsArray;

    /**
     * Configure options resolver to validate and resolve options.
     * @param  OptionsResolver $resolver
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
     * Setup option normalization for all options that use it.
     * @param  OptionsResolver $resolver
     */
    protected function setOptionsNormalizers(OptionsResolver $resolver)
    {
        foreach (['search', 'locate'] as $key) {
            $this->setSearchOptionNormalizer($resolver, $key);
        }
    }

    /**
     * Setup option normalization for a search-type option (search, locate, etc.).
     *
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
     * Returns the *AuthAttempt* preset specified in *$this$ preset's ``'attempt'`` option.
     * @return AuthAttempt
     */
    public function attempt() : AuthAttempt
    {
        return $this->getRelatedPreset(AuthAttempt::class, 'attempt');
    }

    /**
     * Returns an array of *Session* presets listed in *$this* preset's ``'session'`` option.
     * @return array
     */
    public function sessions() : array
    {
        return $this->getRelatedPresetsArray(Session::class, 'sessions');
    }

    /**
     * Returns the *Search* preset specified in *$this* preset's ``'search'`` option.
     * @return Search|null
     */
    public function search() : ?Search
    {
        return $this->getRelatedPreset(Search::class, 'search');
    }

    /**
     * Returns the *Search* preset specified in *$this* preset's ``'locate'`` option.
     * @return Search|null
     */
    public function locate() : ?Search
    {
        return $this->getRelatedPreset(Search::class, 'locate');
    }
}

// vim: syntax=php sw=4 ts=4 et:
