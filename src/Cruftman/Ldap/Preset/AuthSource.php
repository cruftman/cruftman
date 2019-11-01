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
     * @var AuthRequest[]
     */
    protected $requests = [];

    /**
     * Initializes the service object.
     *
     * @param Service $ldapService
     * @param array $options
     */
    public function __construct(Service $ldapService, array $options)
    {
        parent::__construct($ldapService, $options);
        $this->initRequests($options['requests']);
    }

    protected function initRequests($requestsOptions)
    {
        $this->requests = $this->createRequests($requestsOptions);
    }

    protected function createRequests(array $requestsOptions)
    {
        $service = $this->getLdapService();
        $requests = $requestsOptions;
        array_walk($requests, function (&$request, $key) use ($service) {
            $request = new AuthRequest($service, $request);
        });
        return $requests;
    }

    /**
     * Get the array of auth request templates.
     *
     * @return array
     */
    public function getRequests() : array
    {
        return $this->requests;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['requests'])
                 ->setAllowedTypes('requests', 'array[]');
    }
}

// vim: syntax=php sw=4 ts=4 et:
