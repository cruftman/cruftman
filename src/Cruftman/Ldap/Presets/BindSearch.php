<?php
/**
 * @file src/Cruftman/Ldap/Presets/BindSearch.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Presets;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Search preset for post-bind operations.
 */
class BindSearch extends Search
{
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setDefault('base', '${binddn}')
                 ->setDefault('filter', 'objectclass=*')
                 ->setDefault('options', ['scope' => 'base', 'attributes' => ['*']])
                 ->setAllowedTypes('base', 'string')
                 ->setAllowedTypes('filter', 'string')
                 ->setAllowedTypes('options', 'array');
        $resolver->setNormalizer('options', function (Options $options, $searchOptions) {
            if (!array_key_exists('scope', $searchOptions)) {
                $searchOptions['scope'] = 'base';
            }
            if (!array_key_exists('attributes', $searchOptions)) {
                $searchOptions['attributes'] = ['*'];
            }
            return $searchOptions;
        });
    }
}

// vim: syntax=php sw=4 ts=4 et:
