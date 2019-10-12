<?php

namespace Cruftman\JsonApi\Resources\Accounts;

class Adapter extends \Cruftman\JsonApi\Eloquent\Adapter
{
    protected $modelClass = \Cruftman\Models\Account::class;

    protected function users()
    {
        return $this->hasMany();
    }
}
