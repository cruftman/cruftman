<?php
/**
 * @file src/Cruftman/Ldap/Preset/AuthRequest.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Cruftman\Ldap\Service;
use Cruftman\Support\Traits\ValidatesOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @todo Write documentation.
 */
class AuthRequest extends AbstractPreset
{
    use ValidatesOptions;

    /**
     * Configure options resolver to validate and resolve options.
     *
     * @param  OptionsResolver $resolver
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['connection', 'bind'])
                 ->setDefined(['search'])
                 ->setAllowedTypes('connection', 'string')
                 ->setAllowedTypes('bind', 'string')
                 ->setAllowedTypes('search', 'string');
    }

    /**
     * Creates the actual query and executes it.
     *
     * @param  array $arguments
     */
    public function execute(array $credentials = [])
    {
        if ($this->getOption('search') === null) {
            return [$this->directBind($credentials)];
        } else {
            return $this->searchBind($credentials);
        }
    }

    /**
     * Returns the template search query being part of the auth request.
     */
    public function getSearchQuery() : SearchQuery
    {
        $name = $this->getOptionOrFail('search');
        return $this->getLdapService()->getSearchQuery($name);
    }

    /**
     * Returns the Connection preset used by this object.
     *
     * @return Connection
     */
    public function getConnection() : Connection
    {
        $name = $this->getOptionOrFail('connection');
        return $this->getLdapService()->getConnection($name);
    }

    /**
     * Returns the Binding preset used by this object.
     *
     * @return Binding
     */
    public function getBinding() : Binding
    {
        $name = $this->getOptionOrFail('bind');
        return $this->getLdapService()->getBinding($name);
    }

    protected function directBind(array $credentials)
    {
        $connection = $this->getConnection();
        $binding = $this->getBinding();

        $ldap = $connection->createLdapInterface($credentials);

        try {
            $bound = $binding->bindLdapInterface($ldap, $credentials);
        } catch (LdapException $e) {
            if ($e->getCode() !== 0x31) {
                throw $e;
            }
            // Invalid Credentials
            $bound = false;
        }

        // FIXME: this is ugly, but it's an experiment
        return [
            'instance' => $ldap,
            'credentials' => $credentials,
            'connection' => $connection,
            'binding' => $binding->substOptions($credentials),
            'bound' => $bound
        ];
    }

    protected function searchBind(array $credentials)
    {
        $search = $this->getSearchQuery();
        $entries = $search->execute($credentials)->getEntries(false);

        $result = [];
        foreach ($entries as $entry) {
            $arg = array_merge($credentials, ['dn' => $entry->getDn()]);
            $result[] = array_merge($this->directBind($arg), ['entry' => $entry]);
        }
        return $result;
    }
}

// vim: syntax=php sw=4 ts=4 et:
