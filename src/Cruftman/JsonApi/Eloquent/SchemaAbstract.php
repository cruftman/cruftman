<?php

namespace Cruftman\JsonApi\Eloquent;

use Neomerx\JsonApi\Schema\SchemaProvider;

abstract class SchemaAbstract extends SchemaProvider
{
    /**
     * @param $record
     *      the domain record being serialized.
     * @return string
     */
    public function getId($record)
    {
        return (string) $record->getRouteKey();
    }
}
