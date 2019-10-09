<?php
/**
 * @file src/Cruftman/Transformers/PersonTransformer.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Transformers;

use Cruftman\Models\Person;
use League\Fractal\ParamBag;

class PersonTransformer extends ModelTransformer
{
    protected $availableIncludes = [
        'occupied_locations'
    ];

    public function includeOccupiedLocations(Person $person, ParamBag $params = null)
    {
        return $this->createFractalResource($person->occupied_locations);
    }
}

// vim: syntax=php sw=4 ts=4 et:
