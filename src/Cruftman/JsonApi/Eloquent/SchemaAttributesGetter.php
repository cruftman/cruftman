<?php

namespace Cruftman\JsonApi\Eloquent;

class SchemaAttributesGetter
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Initializes the object.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    protected function setConfig($config)
    {
        $list = array_filter($config, \is_int::class, ARRAY_FILTER_USE_KEY);
        $this->config = array_diff_key($config, $list);

        if (!isset($this->config['only'])) {
            $onlyList = array_filter($list, function ($s) {
                return preg_match('/^([[:alpha:]][\w-]*)$/', $s);
            });
            $this->config['only'] = $onlyList ?: null;
        }

        if (!isset($this->config['except'])) {
            $exceptList = array_filter(array_map(function ($s) {
                return preg_filter('/^!([[:alpha:]][\w-]*)$/', '\1', $s) ?? false;
            }, $list));
            $this->config['except'] = $exceptList;
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     *
     * @return array
     */
    public function get($record)
    {
        $only = $this->config['only'] ?? null;

        $except = [$record->getRouteKeyName()];
        $except = array_merge($this->config['except'] ?? [], $except);

        $attributes = $this->getAttributes($record, $only, $except);

        $attributes = $this->convertAttributes($attributes, $this->config['convert'] ?? null);

        return $this->mapKeys($attributes, $this->config['map'] ?? null);
    }

    protected function getAttributes($record, ?array $only, array $except)
    {
        if (!isset($only)) {
            $only = array_keys($record->attributesToArray());
        }
        $attributes = array_fill_keys(array_diff($only, $except), null);
        array_walk($attributes, function (&$value, $key) use ($record) {
            $value = $record->{$key};
        });
        return $attributes;
    }

    protected function convertAttributes(array $attributes, ?array $converters)
    {
        if (!isset($converters)) {
            return $attributes;
        }

        array_walk($attributes, function (&$value, $key) use ($converters) {
            $value = $this->convertAttribute($value, $converters[$key] ?? null);
        });

        return $attributes;
    }

    protected function convertAttribute($value, $converter)
    {
        if (!isset($converter) || !isset($value)) {
            return $value;
        }

        if (is_object($value) && is_callable([$value, $converter])) {
            // Example: $converter = 'toRfc3339String' yields $value->toRfc3339String().
            return call_user_func([$value, $converter]);
        } elseif (is_callable($converter)) {
            // Example: $converter($value)
            return call_user_func($converter, $value);
        }

        return $value;
    }

    protected function mapKeys(array $attributes, ?array $map)
    {
        if(!isset($map)) {
            return $attributes;
        }

        $mapped = [];
        foreach ($attributes as $key => $value) {
            $mapped[$map[$key] ?? $key] = $value;
        }
        return $mapped;
    }
}
