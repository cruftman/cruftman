<?php

namespace Cruftman\JsonApi\Resources\Accounts;

use Cruftman\JsonApi\Eloquent\AbstractSchema;

class Schema extends AbstractSchema
{
    /**
     * @var string
     */
    protected $resourceType = 'accounts';

    /**
     * @var array
     */
    protected $relationships = ['users'];
}
