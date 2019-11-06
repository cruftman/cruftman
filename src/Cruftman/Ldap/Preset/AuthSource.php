<?php
/**
 * @file src/Cruftman/Ldap/Preset/AuthSource.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Korowai\Lib\Ldap\Adapter\AdapterInterface;
use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Exception\LdapException;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Ldap\Service;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @todo Write documentation
 */
class AuthSource extends AbstractPreset
{
    use ValidatesOptions;

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['attempt'])
                 ->setDefined(['sessions', 'search'])
                 ->setAllowedTypes('sessions', 'array')
                 ->setAllowedTypes('search', ['string', 'array'])
                 ->setDefault('attempt', function (OptionsResolver $nested) {
                     $nested->setRequired(['bind'])
                            ->setDefined(['connections'])
                            ->setAllowedTypes('connections', 'array')
                            ->setAllowedTypes('bind', ['string', 'array']);
                 });
    }

    /**
     * @todo Write documentation.
     * @return Session[]
     */
    public function getSessions() : array
    {
        $service = $this->getLdapService();
        return array_map(function ($sessionOptions) use ($service) {
            return $service->getSession($sessionOptions);
        }, $this->getOption('sessions', []));
    }

    /**
     * @todo Write documentation
     * @return Search|null
     */
    public function getSearch() : ?Search
    {
        $service = $this->getLdapService();
        if (($searchOptions = $this->getOption('search')) === null) {
            return null;
        }
        return $service->getSearch($searchOptions);
    }

    /**
     * @todo Write documentation
     * @return Connection[]
     */
    public function getAttemptConnections() : array
    {
        $service = $this->getLdapService();
        return array_map(function ($connectionOptions) use ($service) {
            return $service->getConnection($connectionOptions);
        }, $this->getOption('attempt.connections', []));
    }

    /**
     * @todo Write documentation
     * @return Binding
     */
    public function getAttemptBinding() : Binding
    {
        $service = $this->getLdapService();
        return $service->getBinding($this->getOptionOrFail('attempt.bind'));
    }
}

// vim: syntax=php sw=4 ts=4 et:
