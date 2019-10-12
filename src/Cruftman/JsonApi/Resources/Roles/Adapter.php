<?php

namespace Cruftman\JsonApi\Resources\Roles;

class Adapter extends \Cruftman\JsonApi\Eloquent\Adapter
{
    protected $modelClass = \Cruftman\Models\Role::class;

    protected function users()
    {
        return $this->hasMany();
    }

    protected function policies()
    {
        return $this->hasMany();
    }
}
