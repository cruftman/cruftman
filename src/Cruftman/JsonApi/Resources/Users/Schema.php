<?php

namespace Cruftman\JsonApi\Resources\Users;

use Cruftman\JsonApi\Eloquent\AbstractSchema;

class Schema extends AbstractSchema
{
    /**
     * @var string
     */
    protected $resourceType = 'users';

    /**
     * @var array
     */
    protected $relationships = ['person', 'password', 'roles'];
}
