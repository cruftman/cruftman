<?php

namespace Cruftman\JsonApi\Resources\Locations;

use Cruftman\JsonApi\Eloquent\AbstractSchema;

class Schema extends AbstractSchema
{
    /**
     * @var string
     */
    protected $resourceType = 'locations';

    /**
     * @var string
     */
    protected $relathionships = [ 'occupants' ];
}
