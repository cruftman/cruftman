<?php
/**
 * @file src/Cruftman/Auth/UserProvider.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Auth;

//use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\EloquentUserProvider;

class UserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        dump(['retrieveById', $identifier]);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        dump(['retrieveByToken', $identifier, $token]);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        dump(['updateRememberToken', $user, $token]);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        dump(['retrieveByCredentials', $credentials]);
        if (($user = parent::retrieveByCredentials($credentials)) === null) {
            $user = $this->addFromLdap($credentials);
        }
        dump(['retrieveByCredentials -> ', $user]);
        return $user;
    }


    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        dump(['validateCredentials', $user, $credentials]);
    }

    protected function addFromLdap($credentials)
    {
    }
}

// vim: syntax=php sw=4 ts=4 et:
