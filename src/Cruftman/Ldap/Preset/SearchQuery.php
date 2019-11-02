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
use Korowai\Lib\Ldap\Exception\LdapException;
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

    public function isFallbackError(LdapException $exception)
    {
        static $fallbackCodes = [-1];
        return in_array($exception->getErrorCode(), $fallbackCodes);
    }

    /**
     * Creates and returns the actual search query.
     *
     * @param  array $arguments
     * @return SearchQueryInterface
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

    public function createFallbackSearchQuery(array $arguments = []) : ?self
    {
        $instance = $this->substOption('instance', $arguments);
        $ldap = $this->getLdapService()->ldap($instance);
        if (($fallbackName = $ldap->getOption('fallback.instance')) == null) {
            return null;
        }
        $options = array_merge($this->getOptions()->getArrayCopy(), ['instance' => $fallbackName]);
        return new self($this->getLdapService(), $options);
    }

    /**
     * Creates the actual query and executes it.
     *
     * @param  array $arguments
     * @return ResultInterface
     */
    public function execute(array $arguments = []) : ResultInterface
    {
        return $this->createSearchQuery($arguments)->execute();
    }

    /**
     */
    public function executeWithFallback(array $arguments = []) : ResultInterface
    {
        try {
            return $this->execute($arguments);
        } catch (LdapException $e) {
            if (!$this->isFallbackError($e) ||
                ($fallback = $this->createFallbackSearchQuery($arguments)) === null) {
                throw $e;
            }
            return $fallback->executeWitFallback($arguments);
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
