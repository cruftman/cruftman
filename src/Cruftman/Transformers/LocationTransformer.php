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

use Cruftman\Models\Location;

class LocationTransformer extends Transformer
{
    /**
     * List of resources possible to include in json response.
     *
     * @var array
     */
    protected $availableIncludes = [
        'people'
    ];

    /**
     * Turns this item object into a generic array
     *
     * @return array
     */
    public function transform(Location $location)
    {
        return $location->toArray();
    }

    /**
     * Include People
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includePeople(Location $location)
    {
        return $this->collection($location->people, new PersonTransformer, 'person');
    }
}

// vim: syntax=php sw=4 ts=4 et:
