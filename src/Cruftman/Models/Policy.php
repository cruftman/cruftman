<?php
/**
 * @file src/Cruftman/Models/Policy.php
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

class Policy extends Model
{
    use SoftDeletes;

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
     * Used by JsonAPI (and perhaps by someone else).
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
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}

// vim: syntax=php sw=4 ts=4 et:
