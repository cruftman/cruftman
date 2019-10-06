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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Location model.
 *
 * It may be a room, a lobby, building, etc.
 */
class Location extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name', 'comment' ];

    /**
     * Persons assigned to the location.
     */
    public function people()
    {
        return $this->belongsToMany(Person::class, 'person_location');
    }
}

// vim: syntax=php sw=4 ts=4 et:
