<?php
/**
 * @file src/Cruftman/Ldap/Presets/Search.php
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
use Cruftman\Support\Preset;
use Cruftman\Support\Traits\ValidatesOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Search preset.
 */
class Search extends Preset
{
    use ValidatesOptions;

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['base', 'filter'])
                 ->setDefined(['options'])
                 ->setAllowedTypes('base', 'string')
                 ->setAllowedTypes('filter', 'string')
                 ->setAllowedTypes('options', 'array');
    }

    /**
     * Returns base DN for the search preset.
     * @param array $arguments
     * @return string
     */
    public function base(array $arguments = []) : string
    {
        return $this->substOptionOrFail('base', $arguments);
    }

    /**
     * Returns search filter for the search.
     * @param array $arguments
     * @return string
     */
    public function filter(array $arguments = []) : string
    {
        return $this->substOptionOrFail('filter', $arguments);
    }

    /**
     * Returns search options for the search.
     * @param array $arguments
     * @return array
     */
    public function options(array $arguments = []) : array
    {
        return $this->substOption('options', $arguments, []);
    }
}

// vim: syntax=php sw=4 ts=4 et:
