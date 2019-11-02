<?php
/**
 * @file src/Cruftman/Ldap/Preset/Ldap.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Korowai\Lib\Ldap\LdapInterface;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Ldap\Service;
use Cruftman\Ldap\Traits\ProvidesLdapInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides all methods of \Korowai\Lib\Ldap\LdapInterface.
 */
class Ldap extends AbstractPreset implements LdapInterface
{
    use ValidatesOptions,
        ProvidesLdapInterface;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Initializes the Ldap object.
     *
     * @param  Service $ldapService
     * @param  array $options
     * @param  array $arguments
     */
    public function __construct(Service $ldapService, array $options, array $arguments = [])
    {
        parent::__construct($ldapService, $options);
        $this->arguments = $arguments;
    }

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['connection'])
                 ->setDefined(['bind', 'fallback'])
                 ->setAllowedTypes('connection', 'string')
                 ->setAllowedTypes('bind', 'string')
                 ->setDefault('fallback', function (OptionsResolver $nested) {
                     $nested->setRequired('instance')
                            ->setDefined('errors')
                            ->setAllowedTypes('instance', 'string')
                            ->setAllowedTypes('errors', 'array');
                 });
    }

    /**
     * Supports the ProvidesLdapInterface trait.
     *
     * @return LdapInterface
     */
    protected function createLdapInterface() : LdapInterface
    {
        $service = $this->getLdapService();

        $connectionName = $this->getOptionOrFail('connection');
        $connection = $service->connection($connectionName);

        $ldapInterface = $connection->createLdapInterface($this->arguments);

        if (($bindingName = $this->getOption('bind')) !== null)  {
            $binding = $service->binding($bindingName);
            $binding->bindLdapInterface($ldapInterface, $this->arguments);
        }

        return $ldapInterface;
    }

    /**
     * Returns fallback instance or null if there is no fallback.
     *
     * @return Ldap|null
     */
    public function getFallbackLdap() : ?Ldap
    {
        if (($fallbackName = $this->getOption('fallback.instance')) === null) {
            return null;
        }
        return $this->getLdapService()->ldap($fallbackName);
    }
}

// vim: syntax=php sw=4 ts=4 et:
