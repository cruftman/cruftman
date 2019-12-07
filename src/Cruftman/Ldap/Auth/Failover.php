<?php
/**
 * @file src/Cruftman/Ldap/Traits/Failover.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Auth;

use Korowai\Lib\Ldap\Exception\LdapException;

/**
 * Simple failover algorithm.
 */
class Failover
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var callable
     */
    protected $failureHandler;

    /**
     * Initializes the object.
     *
     * @param callable $callback
     * @param callable $failureHandler
     */
    public function __construct(callable $callback, callable $failureHandler)
    {
        $this->setCallback($callback);
        $this->setFailureHandler($failureHandler);
    }

    /**
     * @todo Write documentation
     * @return Failover $this
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @todo Write documentation
     * @return callable|null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @todo Write documentation
     * @return Failover $this
     */
    public function setFailureHandler(callable $failureHandler)
    {
        $this->failureHandler = $failureHandler;
        return $this;
    }

    /**
     * @todo Write documentation
     * @return callable|null
     */
    public function getFailureHandler()
    {
        return $this->failureHandler;
    }

    /**
     * Try multiple providers.
     *
     * @param array $providers
     * @param array $arguments
     */
    public function __invoke(array $providers, array $arguments)
    {
        $callback = $this->getCallback();
        foreach ($providers as $provider) {
            try {
                return call_user_func_array($callback,  [$provider, $arguments]);
            } catch (LdapException $exception) {
                $this->rethrowIfUnrecoverable($exception);
            }
        }
        return call_user_func_array($this->getFailureHandler(), [$providers, $arguments]);
    }

    /**
     * Rethrow the $exception if can't be recovered with failover.
     *
     * @param  LdapException $exception
     * @throws LdapException
     */
    protected function rethrowIfUnrecoverable(LdapException $exception)
    {
        if ($exception->getCode() !== -1) {
            throw $exception;
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
