<?php

namespace Cruftman\JsonApi\Resources\People;

class Adapter extends \Cruftman\JsonApi\Eloquent\Adapter
{
    protected $modelClass = \Cruftman\Models\Person::class;

    protected function occupiedLocations()
    {
        return $this->hasMany();
    }
}
