<?php

namespace Cruftman\JsonApi\Locations;

class Adapter extends \Cruftman\JsonApi\Adapter
{
    protected $modelClass = \Cruftman\Models\Location::class;

    protected function occupants()
    {
        return $this->hasMany();
    }
}
