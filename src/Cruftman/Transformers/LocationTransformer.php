<?php
/**
 * @file src/Cruftman/Transformers/LocationTransformer.php
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
use League\Fractal\ParamBag;

class LocationTransformer extends ModelTransformer
{
    protected $modelName = 'Location';

    /**
     * List of resources possible to include in json response.
     *
     * @var array
     */
    protected $availableIncludes = [
        'occupants'
    ];

    /**
     * Include People
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function includeOccupants(Location $location, ParamBag $params = null)
    {
        return $this->transformCollection($location->occupants, null, $params);
    }
}

// vim: syntax=php sw=4 ts=4 et:
