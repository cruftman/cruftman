<?php
/**
 * @file src/Cruftman/Models/User.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword/*, MustVerifyEmail*/;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be hiddent for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token'
    ];

    /**
     * @var array
     */
    protected $visible = [
        'person_id',
        'name'
    ];

    /**
     * @todo Write documentation
     */
    public function person()
    {
        return $this->hasOne(Person::class);
    }

    /**
     * @todo Write documentation
     */
    public function password()
    {
        return $this->hasOne(Password::class);
    }

    /**
     * @todo Write documentation
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the password for the user.
     *
     * @return string|null
     */
    public function getAuthPassword()
    {
        if (($password = $this->password) === null) {
            // no password assigned.
            return null;
        }
        // TODO: disabled? expired?
        return $password->password;
    }
}

// vim: syntax=php sw=4 ts=4 et:
