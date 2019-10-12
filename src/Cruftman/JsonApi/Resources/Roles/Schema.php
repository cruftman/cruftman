<?php

namespace Cruftman\JsonApi\Resources\Roles;

use Cruftman\JsonApi\Eloquent\AbstractSchema;

class Schema extends AbstractSchema
{
    /**
     * @var string
     */
    protected $resourceType = 'roles';

    /**
     * @var array
     */
    protected $relationships = ['users', 'policies'];
}
