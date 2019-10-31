<?php
/**
 * @file src/Cruftman/Ldap/LdapInstance.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Korowai\Lib\Ldap\Ldap;
use Korowai\Lib\Ldap\LdapInterface;

use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Support\Traits\ValidatesOptions;

use Cruftman\Ldap\Traits\HasLdapService;
use Cruftman\Ldap\Traits\ProvidesLdapInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides all methods of \Korowai\Lib\Ldap\LdapInterface.
 */
class LdapInstance implements LdapInterface
{
    use HasTemplateOptions,
        ValidatesOptions,
        HasLdapService,
        ProvidesLdapInterface;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Initializes the LdapInstance object.
     *
     * @param  Service $ldap ldap service
     * @param  array $options
     * @param  array $arguments
     */
    public function __construct(Service $ldapService, array $options, array $arguments = [])
    {
        $this->setLdapService($ldapService);
        $this->setOptions($options);
        $this->arguments = $arguments;
    }

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['connection'])
                 ->setDefined(['bind'])
                 ->setAllowedTypes('connection', 'string')
                 ->setAllowedTypes('bind', 'string');
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
        $connection = $service->getConnection($connectionName);

        $ldapInterface = $connection->createLdapInterface($this->arguments);

        if (($bindingName = $this->getOption('bind')) !== null)  {
            $binding = $service->getBinding($bindingName);
            $binding->bindLdapInterface($ldapInterface, $this->arguments);
        }

        return $ldapInterface;
    }
}

// vim: syntax=php sw=4 ts=4 et:
