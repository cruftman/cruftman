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

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
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
}

// vim: syntax=php sw=4 ts=4 et:
