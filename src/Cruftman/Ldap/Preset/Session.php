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
     * Create new instance of LdapInterface.
     *
     * @param  array $arguments
     * @return LdapInterface
     */
    public function createLdap(array $arguments = []) : LdapInterface
    {
        $service = $this->getLdapService();

        $connection = $service->connection($this->substOptionOrFail('connection', $arguments));

        $ldap = $connection->createLdap($arguments);

        if (($bindOption = $this->substOption('bind', $arguments)) !== null)  {
            $binding = $service->binding($bindOption);
            $binding->bindLdapInterface($ldap, $arguments);
        }

        return $ldap;
    }
}

// vim: syntax=php sw=4 ts=4 et:
