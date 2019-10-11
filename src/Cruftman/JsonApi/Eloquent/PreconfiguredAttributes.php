<?php

namespace Cruftman\JsonApi\Eloquent;

trait PreconfiguredAttributes
{
    public function getAttributes($record)
    {
        return (new SchemaAttributesGetter($this->attributes ?? []))->get($record);
    }
}
