<?php

namespace Cruftman\JsonApi\Resources\Roles;

class Validators extends \Cruftman\JsonApi\Validators
{
    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *      the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = ['users', 'policies'];
}
