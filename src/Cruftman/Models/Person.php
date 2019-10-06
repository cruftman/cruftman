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

/**
 *  @OA\Schema(
 *      description="Person model",
 *      type="object",
 *      title="Person model",
 *      @OA\Xml(name="Person"),
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="uid", type="string"),
 *      @OA\Property(property="givenname", type="string"),
 *      @OA\Property(property="surname", type="string"),
 *      @OA\Property(property="nickname", type="string"),
 *      @OA\Property(property="birthday", type="string"),
 *      @OA\Property(property="comment", type="string"),
 *      @OA\Property(property="created_at", type="string", format="date-time"),
 *      @OA\Property(property="updated_at", type="string", format="date-time"),
 *      @OA\Property(property="deleted_at", type="string", format="date-time")
 *  )
 */
class Person extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uid', 'givenname', 'surname', 'nickname', 'birthday', 'comment'];

    /**
     * Locations assigned to the person.
     */
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'person_location');
    }
}

// vim: syntax=php sw=4 ts=4 et:
