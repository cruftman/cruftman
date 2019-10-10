<?php

namespace Cruftman\JsonApi\Resources\Locations;

class Adapter extends \Cruftman\JsonApi\Adapter
{
    protected $modelClass = \Cruftman\Models\Location::class;

    protected function occupants()
    {
        return $this->hasMany();
    }
}
