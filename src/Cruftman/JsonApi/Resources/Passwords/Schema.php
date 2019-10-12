<?php

namespace Cruftman\JsonApi\Resources\Passwords;

use Cruftman\JsonApi\Eloquent\AbstractSchema;

class Schema extends AbstractSchema
{
    /**
     * @var string
     */
    protected $resourceType = 'passwords';

    /**
     * @var array
     */
    protected $relationships = ['user'];
}
