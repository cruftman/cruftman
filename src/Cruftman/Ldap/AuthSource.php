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

use Korowai\Lib\Ldap\Exception\LdapException;

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

    protected function isValidSequence(array $array)
    {
        foreach ($array as $step) {
            if (!is_string($step['connection'] ?? null) || !is_string($step['bind'] ?? null)) {
                return false;
            }
            $search = $step['search'] ?? null;
            if (!is_string($search) && !is_null($search)) {
                return false;
            }
        }
        return true;
    }

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['sequence'])
                 ->setAllowedTypes('sequence', 'array[]')
                 ->setAllowedValues('sequence', function ($array) {
                     return $this->isValidSequence($array);
                 });
    }

    public function attempt(array $credentials)
    {
        $result = [];
        $sequence = $this->substOptionOrFail('sequence', $credentials);
        foreach ($sequence as $step) {
            $result = array_merge($result, $this->makeStep($step, $credentials));
        }
        return $result;
    }

    protected function makeStep(array $step, array $credentials)
    {
        if (($search = $step['search'] ?? null) === null) {
            return [$this->tryDirectBind($step, $credentials)];
        } else {
            return $this->trySearchBind($step, $credentials);
        }
    }

    protected function tryDirectBind(array $step, array $credentials)
    {
        $ldapService = $this->getLdapService();
        $connectionTemplate = $ldapService->getConnection($step['connection']);
        $bindingTemplate = $ldapService->getBinding($step['bind']);

        $ldap = $connectionTemplate->createLdapInstance($credentials);

        try {
            $bindResult = $bindingTemplate->bindLdapInstance($ldap, $credentials);
        } catch (LdapException $e) {
            if ($e->getCode() !== 0x31) {
                throw $e;
            }
            // Invalid Credentials
            $bindResult = false;
        }

        return [
            'instance' => $ldap,
            'credentials' => $credentials,
            'connection' => $connectionTemplate,
            'binding' => $bindingTemplate,
            'bindResult' => $bindResult
        ];
    }

    protected function trySearchBind(array $step, array $credentials)
    {
        $ldapService = $this->getLdapService();
        $searchTemplate = $ldapService->getSearchQuery($step['search']);
        $entries = $searchTemplate->execute($credentials)->getEntries(false);

        $result = [];
        foreach ($entries as $entry) {
            $arguments = array_merge($credentials, ['dn' => $entry->getDn()]);
            $result[] = array_merge(
                $this->tryDirectBind($step, $arguments),
                ['attributes' => $entry->getAttributes()]
            );
        }
        return $result;
    }

    /*
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
     */
}

// vim: syntax=php sw=4 ts=4 et:
