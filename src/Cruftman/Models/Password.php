<?php
/**
 * @file src/Cruftman/Models/Password.php
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

class Password extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];


    /**
     * Used by JsonAPI (and perhaps by someone else).
     *
     * @var array
     */
    protected $visible = [
        'login',
        'expires_at',
        'disabled'
    ];

    protected $hidden = [
        'password'
    ];

    /**
     * @todo Write documentation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// vim: syntax=php sw=4 ts=4 et:
