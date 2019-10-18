<?php
/**
 * @file src/Cruftman/Models/Person.php
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

class Person extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'personal_id',
        'first_name',
        'last_name',
        'birthday',
        'gender',
        'title',
        'comment'
    ];


    /**
     * @var array
     */
    protected $visible = [
        'personal_id',
        'first_name',
        'last_name',
        'birthday',
        'gender',
        'title',
        'comment'
    ];

    /**
     * Locations occuppied by the person.
     */
    public function occupiedLocations()
    {
        return $this->belongsToMany(Location::class, 'location_occupant');
    }

    /**
     * @todo Write documentation.
     */
    public function users()
    {
        return $this->belongsToMany(Person::class);
    }
}

// vim: syntax=php sw=4 ts=4 et:
