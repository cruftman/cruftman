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

//use Korowai\Lib\Ldap\LdapInterface;
//use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
//use Korowai\Lib\Ldap\Adapter\ResultInterface;

use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Support\Traits\ValidatesOptions;

use Cruftman\Ldap\Traits\HasLdapService;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @todo Write documentation
 */
class AuthSource
{
    use HasTemplateOptions,
        ValidatesOptions,
        HasLdapService;

    /**
     * Initializes the service object.
     *
     * @param Service $ldap ldap service
     * @param array $config
     */
    public function __construct(Service $ldapService, array $options)
    {
        $this->setLdapService($ldapService);
        $this->setOptions($options);
    }

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['attempt'])
                 ->setDefined(['search', 'notfound', 'unavailable'])
                 ->setAllowedTypes('attempt', 'array')
                 ->setAllowedTypes('search', ['string', 'string[]'])
                 ->setAllowedTypes('notfound', 'string')
                 ->setAllowedTypes('unavailable', 'string')
                 ->setAllowedValues('notfound', ['stop', 'next'])
                 ->setAllowedValues('unavailable', ['stop', 'next']);
    }

    public function attempt(array $credentials)
    {
        throw \BadMethodCallException('not implemented');
    }

    public function search(array $credentials)
    {
        $options = $this->getOptions();
        if ($this->getOption('search') !== null) {
            $queries = $this->getSearchQueries($credentials);
            $entries = $this->executeSearchQueries($queries, $credentials);
        } else {
            $entries = [];
        }
        return $entries;
    }

    protected function getSearchQueries(array $credentials)
    {
        $names = $this->substOption('search', $credentials);
        if (is_string($names)) {
            $names = [$names];
        }
        return array_map(function ($name) {
            return $this->getLdapService()->getSearchQuery($name);
        }, $names);
    }

    protected function executeSearchQueries(array $queries, array $credentials)
    {
        $entries = [];
        foreach ($queries as $query) {
            $result = $query->execute($credentials);
            $entries = array_merge($entries, $result->getEntries(false));
        }
        return $entries;
    }
}

// vim: syntax=php sw=4 ts=4 et:
