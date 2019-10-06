<?php
/**
 * @file src/Cruftman/Transformers/Transformer.php
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

class PersonTransformer extends Transformer
{
    protected $availableIncludes = [
        'locations'
    ];

    public function transform(Person $person)
    {
        return $person->toArray();
    }

    public function includeLocations(Person $person)
    {
        return $this->collection($person->locations, new LocationTransformer);
    }
}

// vim: syntax=php sw=4 ts=4 et:
