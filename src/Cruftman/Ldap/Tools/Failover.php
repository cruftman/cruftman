<?php
/**
 * @file src/Cruftman/Ldap/Tools/Failover.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Tools;

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
    protected $fallback;

    /**
     * Initializes the object.
     *
     * @param callable $callback
     * @param callable $fallback
     */
    public function __construct(callable $callback, callable $fallback = null)
    {
        $this->setCallback($callback);
        $this->setFallback($fallback);
    }

    /**
     * Sets *$callback* called by the failover algorithm.
     * @param  callable|null $callback
     * @return Failover $this
     */
    public function setCallback(?callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Return the callback set with *setCallback()*.
     * @return callable|null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets the callack called after all providers fail.
     *
     * @param  callable|null $fallback
     * @return Failover $this
     */
    public function setFallback(?callable $fallback)
    {
        $this->fallback = $fallback;
        return $this;
    }

    /**
     * Returns the failure handling callback provided with *setFallback()*.
     *
     * @return callable|null
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Try multiple providers.
     *
     * @param array $providers
     * @param array $arguments
     */
    public function tryWith(array $providers)
    {
        $callback = $this->getCallback();
        foreach ($providers as $provider) {
            try {
                return call_user_func_array($callback, [$provider]);
            } catch (LdapException $exception) {
                $this->rethrowIfUnrecoverable($exception);
            }
        }
        $fallback = $this->getFallback();
        return $fallback ? call_user_func_array($fallback, [$providers]) : null;
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
