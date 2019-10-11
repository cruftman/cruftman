<?php

namespace Cruftman\JsonApi\Resources\People;

use Cruftman\JsonApi\Eloquent\PreconfiguredAttributes;
use Cruftman\JsonApi\Eloquent\SchemaAbstract;

class Schema extends SchemaAbstract
{
    use PreconfiguredAttributes;

    /**
     * @var string
     */
    protected $resourceType = 'people';

    /**
     * @var array
     */
    protected $relationships = [
    ];

    /**
     * @var array
     */
    protected $attributes = [
    ];
}
