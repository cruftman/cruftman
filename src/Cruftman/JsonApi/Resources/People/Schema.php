<?php

namespace Cruftman\JsonApi\Resources\People;

class Schema extends \Cruftman\JsonApi\Schema
{
    /**
     * @var string
     */
    protected $resourceType = 'people';

    /**
     * @var array
     */
    protected $relationships = [
        'occupied_locations' => [ self::DATA_ATTRIBUTE => 'occupiedLocations' ]
    ];
}
