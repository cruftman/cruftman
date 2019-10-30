<?php
/**
 * @file src/Cruftman/Ldap/AuthRequestTemplate.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

//use Korowai\Lib\Ldap\LdapInterface;
//use Korowai\Lib\Ldap\Adapter\AuthRequestInterface;
//use Korowai\Lib\Ldap\Adapter\ResultInterface;

use Cruftman\Support\Traits\HasTemplateOptions;
use Cruftman\Support\Traits\ValidatesOptions;

use Cruftman\Ldap\Traits\HasLdapService;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Parametrized LDAP search query.
 *
 * The actual query is created by providing additional arguments.
 */
class AuthRequestTemplate
{
    use HasTemplateOptions,
        ValidatesOptions,
        HasLdapService;

    /**
     * Initializes the service object.
     *
     * @param Service $ldap ldap service
     * @param array $templateOptions
     */
    public function __construct(Service $ldapService, array $options)
    {
        $this->setLdapService($ldapService);
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
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
    public function getSearchQuery()
    {
        $name = $this->getOptionOrFail('search');
        return $this->getLdapService()->getSearchQuery($name);
    }

    public function getConnection()
    {
        $name = $this->getOptionOrFail('connection');
        return $this->getLdapService()->getConnection($name);
    }

    public function getBinding()
    {
        $name = $this->getOptionOrFail('bind');
        return $this->getLdapService()->getBinding($name);
    }

    protected function directBind(array $credentials)
    {
        $connectionTemplate = $this->getConnection();
        $bindingTemplate = $this->getBinding();

        $ldap = $connectionTemplate->createLdapInstance($credentials);

        try {
            $bound = $bindingTemplate->bindLdapInstance($ldap, $credentials);
        } catch (LdapException $e) {
            if ($e->getCode() !== 0x31) {
                throw $e;
            }
            // Invalid Credentials
            $bound = false;
        }

        return [
            'instance' => $ldap,
            'credentials' => $credentials,
            'connection' => $connectionTemplate,
            'binding' => $bindingTemplate->substOptions($credentials),
            'bound' => $bound
        ];
    }

    protected function searchBind(array $credentials)
    {
        $searchTemplate = $this->getSearchQuery();
        $entries = $searchTemplate->execute($credentials)->getEntries(false);

        $result = [];
        foreach ($entries as $entry) {
            $arg = array_merge($credentials, ['dn' => $entry->getDn()]);
            $result[] = array_merge($this->directBind($arg), ['entry' => $entry]);
        }
        return $result;
    }
}

// vim: syntax=php sw=4 ts=4 et:
