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

/**
 * Search preset.
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
    }
//
//    /**
//     * Returns base DN for the search preset.
//     * @param  array $arguments
//     * @return string
//     */
//    public function base(array $arguments) : string
//    {
//        return $this->substOption('base', $arguments, '${binddn}');
//    }
//
//    /**
//     * Returns search filter for the search.
//     * @param  array $arguments
//     * @return string
//     */
//    public function filter(array $arguments) : string
//    {
//        return $this->substOption('filter', $arguments, 'objectclass=*');
//    }
//
//    /**
//     * Returns search options for the search.
//     * @param  array $arguments
//     * @return array
//     */
//    public function options(array $arguments) : array
//    {
//        return $this->substOption('options', $arguments, ['scope' => 'base', 'attributes' => ['*']]);
//    }
}

// vim: syntax=php sw=4 ts=4 et:
