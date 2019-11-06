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
                     $nested->setRequired(['connections', 'bind'])
                            ->setAllowedTypes('connections', 'array')
                            ->setAllowedTypes('bind', ['string', 'array']);
                 });
    }

    /**
     * @todo Write documentation.
     * @param  array $arguments
     * @return Session[]
     */
    public function sessions(array $arguments = []) : array
    {
        $service = $this->getLdapService();
        return array_map(function ($sessionOptions) use ($service) {
            return $service->session($sessionOptions);
        }, $this->substOption('sessions', $arguments, []));
    }

    /**
     * @todo Write documentation
     * @param  AdapterInterface $ldap
     * @param  array $arguments
     * @return SearchQueryInterface|null
     */
    public function createSearchQuery(AdapterInterface $ldap, array $arguments = []) : ?SearchQueryInterface
    {
        if (($searchOptions = $this->substOption('search', $arguments)) === null) {
            return null;
        }
        return $this->getLdapService()->search($searchOptions)->createQuery($ldap, $arguments);
    }

    /**
     * @todo Write documentation
     * @param  BindingInterface $ldap
     * @param  array $arguments
     */
    public function attempt(array $arguments = [])
    {
        $service = $this->getLdapService();
        $bindingOptions = $this->getOption('attempt.bind');
        $binding = $service->binding($bindingOptions);

        $connectionsOptions = $this->getOption('attempt.connections');
        foreach ($connectionsOptions as $connectionOptions) {
            $connection = $service->connection($connectionOptions);
            $ldap = $connection->createLdap($arguments);
            try {
                if ($binding->bindLdapInterface($ldap, $arguments) === true) {
                    // FIXME: this is ugly...
                    return [
                        'session' => $service->session(['connection' => $connection, 'binding' => $binding])
                        'ldap' => $ldap,
                        'bound' => true
                    ];
                }
            } catch (LdapException $exception) {
                switch($exception->getCode()) {
                    case 0x31:  // Invalid credentials
                    case -1:    // Connection error or such
                        break;
                    default:
                        throw $exception;
                }
            }
        }
        return null;
    }
}

// vim: syntax=php sw=4 ts=4 et:
