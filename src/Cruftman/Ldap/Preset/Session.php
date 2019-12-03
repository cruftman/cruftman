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
use Cruftman\Support\Preset;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Support\Traits\RelatedPreset;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Session preset.
 */
class Session extends Preset
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
        return $this->getRelatedPresetOrFail(Connection::class, 'connection');
    }

    /**
     * @todo Write documentation
     * @return Binding|null
     */
    public function getBinding() : ?Binding
    {
        return $this->getRelatedPreset(Binding::class, 'bind');
    }

    /**
     * Create new instance of LdapInterface.
     *
     * @param  array $arguments
     * @return LdapInterface
     */
    public function createLdap(array $arguments = []) : LdapInterface
    {
        $connection = $this->getConnection();

        $ldap = $connection->createLdap($arguments);

        if (($binding = $this->getBinding()) !== null) {
            $binding->bindLdapInterface($ldap, $arguments);
        }

        return $ldap;
    }
}

// vim: syntax=php sw=4 ts=4 et:
