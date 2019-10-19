<?php
/**
 * @file src/Cruftman/Models/Role.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Models;

class Role extends Model
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
     * @var array
     */
    protected $visible = [
        'name',
        'comment',
    ];

    /**
     * @todo write documentation
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @todo write documentation
     */
    public function policies()
    {
        return $this->belongsToMany(Policy::class);
    }
}

// vim: syntax=php sw=4 ts=4 et:
