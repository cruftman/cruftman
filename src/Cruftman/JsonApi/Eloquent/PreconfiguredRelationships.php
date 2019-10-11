<?php

namespace Cruftman\JsonApi\Eloquent;

trait PreconfiguredRelationships
{
    public function getRelationships($record, $isPrimary, array $includeRelationships)
    {
        $getter = new SchemaRelationshipsGetter($this->relationships ?? []);
        return $getter->get($record, $isPrimary, $includeRelationships);
    }
}
