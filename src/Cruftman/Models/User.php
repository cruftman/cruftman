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

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Notifications\Notifiable;

class User extends AuthUser
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password'
    ];

    /**
     * The attributes that should be hiddent for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

//
//    /**
//     * @todo Write documentation
//     */
//    public function person()
//    {
//        return $this->hasOne(Person::class);
//    }
//
//    /**
//     * @todo Write documentation
//     */
//    public function password()
//    {
//        return $this->hasOne(Password::class);
//    }
//
//    /**
//     * @todo Write documentation
//     */
//    public function roles()
//    {
//        return $this->belongsToMany(Role::class);
//    }
//
//    /**
//     * Get the password for the user.
//     *
//     * @return string|null
//     */
//    public function getAuthPassword()
//    {
//        if (($password = $this->password) === null) {
//            // no password assigned.
//            return null;
//        }
//        // TODO: disabled? expired?
//        return $password->password;
//    }
}

// vim: syntax=php sw=4 ts=4 et:
