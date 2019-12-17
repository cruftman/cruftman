<?php
/**
 * @file src/Cruftman/Ldap/Presets/AuthAttempt.php
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

/**
 * AuthAttempt preset.
 */
class AuthAttempt extends Preset
{
    use ValidatesOptions,
        RelatedPreset,
        RelatedPresetsArray;

    /**
     * @var Search
     */
    protected $searchPreset = null;

    /**
     * Configures OptionsResolver for the AuthAttempt Preset.
     *
     * @param  OptionsResolver $resolver
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
         $resolver->setRequired(['binding'])
                  ->setDefined(['connections', 'search'])
                  ->setAllowedTypes('binding', ['string', 'array'])
                  ->setAllowedTypes('connections', 'array')
                  ->setAllowedTypes('search', ['string', 'array']);
    }

    /**
     * Returns the Binding Preset as declared in ``'binding'`` option.
     * @return Binding|null
     */
    public function binding() : Binding
    {
        return $this->getRelatedPresetOrFail(Binding::class, 'binding');
    }

    /**
     * Returns array of Connection presets as listed in ``'connections'`` option.
     * @return Connection[]|null
     */
    public function connections() : ?array
    {
        return $this->getRelatedPresetsArray(Connection::class, 'connections', null);
    }

    /**
     * Returns the nested Search preset for additional filtering and entry
     * retrieving.
     *
     * @return Search
     */
    public function search() : ?BindSearch
    {
        return $this->getRelatedPreset(BindSearch::class, 'search');
    }
}

// vim: syntax=php sw=4 ts=4 et:
