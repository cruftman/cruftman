<?php
/**
 * @file src/Cruftman/Models/Account.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Models;

class Account extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'comment'
    ];


    /**
     * May help to build an API around the model.
     *
     * @var array
     */
    protected $visible = [
        'name',
        'comment',
    ];

    /**
     * @todo Write documentation
     */
    public function people()
    {
        return $this->belongsToMany(Person::class);
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
