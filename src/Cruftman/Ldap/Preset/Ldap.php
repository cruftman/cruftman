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
use Cruftman\Ldap\Traits\Resiliency;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides all methods of \Korowai\Lib\Ldap\LdapInterface.
 */
class Ldap extends AbstractPreset implements LdapInterface
{
    use ValidatesOptions,
        ProvidesLdapInterface,
        Resiliency;

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['connection'])
                 ->setDefined(['bind'])
                 ->setAllowedTypes('connection', 'string')
                 ->setAllowedTypes('bind', 'string');
        $this->configureResiliencyOptionsResolver($resolver);
    }

    /**
     * Supports the ProvidesLdapInterface trait.
     *
     * @return LdapInterface
     */
    protected function createLdapInterface(array $arguments = []) : LdapInterface
    {
        $service = $this->getLdapService();

        $connectionName = $this->getOptionOrFail('connection');
        $connection = $service->connection($connectionName);

        $ldapInterface = $connection->createLdapInterface($arguments);

        if (($bindingName = $this->getOption('bind')) !== null)  {
            $binding = $service->binding($bindingName);
            $binding->bindLdapInterface($ldapInterface, $arguments);
        }

        return $ldapInterface;
    }
}

// vim: syntax=php sw=4 ts=4 et:
