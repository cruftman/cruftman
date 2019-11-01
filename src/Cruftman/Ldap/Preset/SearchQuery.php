<?php
/**
 * @file src/Cruftman/Ldap/Preset/SearchQuery.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\ResultInterface;
use Cruftman\Support\Traits\ValidatesOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Parametrized LDAP search query.
 *
 * The actual query is created by providing additional arguments.
 */
class SearchQuery extends AbstractPreset
{
    use ValidatesOptions;


    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['instance', 'base', 'filter'])
                 ->setDefined(['options'])
                 ->setAllowedTypes('instance', 'string')
                 ->setAllowedTypes('base', 'string')
                 ->setAllowedTypes('filter', 'string')
                 ->setAllowedTypes('options', 'array');
    }

    /**
     * Creates and returns the actual search query.
     *
     * @param  array $arguments
     * @return \Korowai\Lib\Ldap\Adapter\SearchQueryInterface
     */
    public function createSearchQuery(array $arguments = []) : SearchQueryInterface
    {
        $base = $this->substOption('base', $arguments);
        $filter = $this->substOption('filter', $arguments);
        $options = $this->substOption('options', $arguments, []);
        $instance = $this->substOption('instance', $arguments);

        $ldap = $this->getLdapService()->ldap($instance);

        return $ldap->createSearchQuery($base, $filter, $options);
    }

    /**
     * Creates the actual query and executes it.
     *
     * @param  array $arguments
     * @return \Korowai\Lib\Ldap\Adapter\ResultInterface
     */
    public function execute(array $arguments = []) : ResultInterface
    {
        return $this->createSearchQuery($arguments)->execute();
    }
}

// vim: syntax=php sw=4 ts=4 et:
