<?php
/**
 * @file src/Cruftman/Model/Location.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Model;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class Location extends Model
{
    public function people()
    {
        return $this->belongsToMany(Person::class, 'person_location');
    }
}

// vim: syntax=php sw=4 ts=4 et:
