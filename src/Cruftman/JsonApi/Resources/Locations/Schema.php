<?php

namespace Cruftman\JsonApi\Resources\Locations;

use Cruftman\JsonApi\Eloquent\SchemaAbstract;
use Cruftman\JsonApi\Eloquent\PreconfiguredAttributes;

class Schema extends SchemaAbstract
{
    use PreconfiguredAttributes;

    /**
     * @var string
     */
    protected $resourceType = 'locations';

    protected $relathionships = [
    ];

    protected $attributes = [
    ];
}
