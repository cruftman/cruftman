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
     * Creates and returns an instance of SearchQueryInterface.
     *
     * @param  AdapterInterface $ldap
     * @param  array $arguments
     * @return SearchQueryInterface
     */
    public function createQuery(AdapterInterface $ldap, array $arguments = []) : SearchQueryInterface
    {
        $base = $this->substOptionOrFail('base', $arguments);
        $filter = $this->substOptionOrFail('filter', $arguments);
        $options = $this->substOption('options', $arguments, []);

        return $ldap->createSearchQuery($base, $filter, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
