<?php

namespace Cruftman\JsonApi;

use Illuminate\Support\Str;

class SchemaRelationships
{
    /** Links information */
    const LINKS = Schema::LINKS;

    /** Linked data key. */
    const DATA = Schema::DATA;

    /** Linked data attribute name. */
    const DATA_ATTRIBUTE = Schema::DATA_ATTRIBUTE;

    /** Relationship meta */
    const META = Schema::META;

    /** If 'self' URL should be shown. */
    const SHOW_SELF = Schema::SHOW_SELF;

    /** If 'related' URL should be shown. */
    const SHOW_RELATED = Schema::SHOW_RELATED;

    /** If data should be shown in relationships. */
    const SHOW_DATA = Schema::SHOW_DATA;

    /**
     * Initializes the object.
     *
     * @param array $relationships
     */
    public function __construct(array $relationships = [])
    {
        $this->relationships = $relationships;
    }

    /**
     * @param $model
     * @param $isPrimary
     * @param array $includeRelationships
     *
     * @return array
     */
    public function get($model, $isPrimary, array $includeRelationships)
    {
        $relationships = [];
        $common = compact('model', 'isPrimary', 'includeRelationships');
        foreach($this->relationships as $key => $specific) {
            $options = $this->makeOptions($key, $common, $specific);
            $relationships[$key] = $this->makeRelationship($options);
        }
        return $relationships;
    }

    protected function makeOptions($key, $common, $specific)
    {
        if (is_string($specific)) {
            $specific = [self::DATA_ATTRIBUTE => $specific];
        } elseif (!is_array($specific)) {
            throw new \InvalidArgumentException(
                sprintf('$relationships[%s] must be an array or a string, not %s', $key, $specific)
            );
        }
        return array_merge($common, $specific, ['key' => $key]);
    }

    protected function makeRelationship(array $options)
    {
        $key = $options['key'];
        $includeRelationships = $options['includeRelationship'];

        $relationship = [
            self::SHOW_SELF => ($options[self::SHOW_SELF] ?? true),
            self::SHOW_RELATED => ($options[self::SHOW_RELATED] ?? true),
            self::SHOW_DATA => (($options[self::SHOW_DATA] ?? true) && isset($includeRelationships[$key]])),
        ];

        if (($dataCallback = $this->makeDataCallback($options)) !== null) {
            $relationship[self::DATA] = $dataCallback;
        }

        return $relationship;
    }

    protected function makeDataCallback(array $options)
    {
        $key = $options['key'];
        $model = $options['model'];

        if (isset($options[self::DATA])) {
            $data = $options[self::DATA];
            return function () use ($data) {
                return $data;
            };
        } elseif (isset($options[self::DATA_METHOD])) {
            $method = $options[self::DATA_METHOD];
            return function () use ($model, $method) {
                return call_user_func([$model, $method]);
            };
        } elseif (isset($options[self::DATA_ATTRIBUTE])) {
            $attribute = $options[self::DATA_ATTRIBUTE];
        } else {
            $attribute = Str::camel($key);
        }
        return function () use ($model, $attribute) {
            return $model->{$attribute};
        };
    }
}
