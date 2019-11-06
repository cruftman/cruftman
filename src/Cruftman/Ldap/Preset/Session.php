<?php
/**
 * @file src/Cruftman/Ldap/Preset/Session.php
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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Session preset.
 */
class Session extends AbstractPreset
{
    use ValidatesOptions;

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['connection'])
                 ->setDefined(['bind'])
                 ->setAllowedTypes('connection', ['string', 'array'])
                 ->setAllowedTypes('bind', ['string', 'array']);
    }

    /**
     * @todo Write documentation
     * @return Connection
     */
    public function getConnection() : Connection
    {
        $service = $this->getLdapService();
        return $service->getConnection($this->getOptionOrFail('connection'));
    }

    /**
     * Create new instance of LdapInterface.
     *
     * @param  array $arguments
     * @return LdapInterface
     */
    public function createLdap(array $arguments = []) : LdapInterface
    {
        $service = $this->getLdapService();

        $connectionOptions = $this->getOptionOrFail('connection');
        $connection = $service->getConnection($connectionOptions);

        $ldap = $connection->createLdap($arguments);

        if (($bindOptions = $this->getOption('bind')) !== null)  {
            $binding = $service->getBinding($bindOptions);
            $binding->bindLdapInterface($ldap, $arguments);
        }

        return $ldap;
    }
}

// vim: syntax=php sw=4 ts=4 et:
