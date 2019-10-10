<?php

namespace Cruftman\JsonApi;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    const DATA_ATTRIBUTE = 'dataAttribute';
    const DATA_METHOD = 'dataMethod';

    protected $relationships = [];

    /**
     * @param $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param $model
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($model)
    {
        return (new SchemaAttributes())->get($model);
    }

    /**
     * Return included relationships.
     */
    public function getRelationships($model, $isPrimary, array $includeRelationships)
    {
        return (new SchemaRelationships($this->relationships))->get($model, $isPrimary, $includeRelationships);
    }
}
