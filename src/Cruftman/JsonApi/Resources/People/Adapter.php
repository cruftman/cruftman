<?php

namespace Cruftman\JsonApi\Resources\People;

class Adapter extends \Cruftman\JsonApi\Adapter
{
    protected $modelClass = \Cruftman\Models\Person::class;

    protected function occupied_locations()
    {
        return $this->hasMany();
    }
}
