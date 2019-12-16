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

use Korowai\Lib\Ldap\LdapInterface;
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
        /*
         $resolver->setRequired(['binding'])
                  ->setDefined(['connections', 'filter', 'attributes', 'retrieve'])
                  ->setAllowedTypes('connections', 'array')
                  ->setAllowedTypes('binding', ['string', 'array'])
                  ->setAllowedTypes('filter', 'string')
                  ->setAllowedTypes('attributes', 'array'); */
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
    public function search() : Search
    {
        return $this->getRelatedPreset(Search::class, 'search');
    }

//    /**
//     * Returns an array of options that may be used to create Search preset.
//     *
//     * @return array
//     */
//    protected function makeSearchOptions() : array
//    {
//        return [
//            'base' => $this->getOptionOrFail('binding'),
//            'filter' => $this->getOption('filter', 'objectclass=*'),
//            'options' => [
//                'scope' => 'base',
//                'attributes' => $this->getOption('attributes', ['*']),
//            ]
//        ];
//    }
//
//    /**
//     * Returns search filter string.
//     *
//     * @param  array $arguments
//     * @return string|null
//     */
//    public function filter(array $arguments) : ?string
//    {
//        return $this->substOption('filter', $arguments);
//    }
//
//    /**
//     * Returns array of attribute names to be returned by successful attempt.
//     *
//     * @param  array $arguments
//     * @return array|null
//     */
//    public function attributes(array $arguments) : ?array
//    {
//        return $this->substOption('attributes', $arguments);
//    }
}

// vim: syntax=php sw=4 ts=4 et:
