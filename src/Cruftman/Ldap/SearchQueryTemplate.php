<?php
/**
 * @file src/Cruftman/Ldap/SearchQueryTemplate.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Korowai\Lib\Ldap\LdapInterface;
//use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\ResultInterface;

use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Support\Traits\ValidatesOptions;

use Cruftman\Ldap\Traits\HasLdapService;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Parametrized LDAP search query.
 *
 * The actual query is created by providing additional arguments.
 */
class SearchQueryTemplate
{
    use HasTemplateOptions,
        ValidatesOptions,
        HasLdapService;

    protected static $templateOptionsResolver = null;

    /**
     * Initializes the service object.
     *
     * @param Service $ldap ldap service
     * @param array $templateOptions
     */
    public function __construct(Service $ldapService, array $options)
    {
        $this->setLdapService($ldapService);
        $this->setOptions($options);
    }

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
     * @return \Korowai\Ldap\SearchQuery
     */
    public function createSearchQuery(array $arguments = []) : SearchQuery
    {
        $base = $this->substOption('base', $arguments);
        $filter = $this->substOption('filter', $arguments);
        $options = $this->substOption('options', $arguments, []);
        $instance = $this->substOption('instance', $arguments);

        $ldap = $this->getLdapService()->getLdapInstance($instance);

        $query = $ldap->createSearchQuery($base, $filter, $options);
        return new SearchQuery($query, $ldap);
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
