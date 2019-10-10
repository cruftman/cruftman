<?php

namespace Cruftman\JsonApi\Locations;

class Schema extends \Cruftman\JsonApi\Schema
{
    /**
     * @var string
     */
    protected $resourceType = 'locations';

    protected $relathionships = [
        'occupants' => [self::DATA_ATTRIBUTE => 'occupants']
    ];
}
