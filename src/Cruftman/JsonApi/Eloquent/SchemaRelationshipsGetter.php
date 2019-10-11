<?php

namespace Cruftman\JsonApi\Eloquent;

class SchemaRelationshipsGetter
{
    const SHOW_SELF = SchemaAbstract::SHOW_SELF;
    const SHOW_RELATED = SchemaAbstract::SHOW_RELATED;
    const DATA = SchemaAbstract::DATA;
    const META = SchemaAbstract::META;

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
        $this->config = $config;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     *
     * @return array
     */
    public function get($record, $isPrimary, array $includeRelationships)
    {
    }

    protected function getRelationship($record, $isPrimary
}
