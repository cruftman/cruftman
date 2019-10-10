<?php

namespace Cruftman\JsonApi;

class SchemaAttributes
{
    /**
     * Initializes the object.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @param $resource
     * @return array
     */
    public function get($resource)
    {
        return array_filter(
            $resource->attributesToArray(),
            function ($attribute) {
                return $attribute != 'id';
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
