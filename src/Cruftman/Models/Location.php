<?php
/**
 * @file src/Cruftman/Models/Location.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Models;

/**
 * Location model.
 *
 * It may be a room, a lobby, building, etc.
 */
class Location extends Model
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
        'comment'
    ];

    /**
     * Persons occupying the location.
     */
    public function occupants()
    {
        return $this->belongsToMany(Person::class, 'location_occupant');
    }
}

// vim: syntax=php sw=4 ts=4 et:
