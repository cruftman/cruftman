<?php

namespace Cruftman\JsonApi\Resources\Users;

class Adapter extends \Cruftman\JsonApi\Eloquent\Adapter
{
    protected $modelClass = \Cruftman\Models\User::class;

    protected function person()
    {
        return $this->hasOne();
    }

    protected function password()
    {
        return $this->hasOne();
    }

    protected function roles()
    {
        return $this->hasMany();
    }
}
