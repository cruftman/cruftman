<?php
/**
 * @file src/Cruftman/Serializers/Transformer.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Serializers;

use League\Fractal\Serializer\JsonApiSerializer as FractalJsonApiSerializer;

class JsonApiSerializer extends FractalJsonApiSerializer
{
    /**
     * {@inheritdoc}
     */
    public function collection($resourceKey, array $data)
    {
        $collection = [];
        if ($this->shouldIncludeLinks()) {
            $collection['links'] = [
                'self' => "{$this->baseUrl}/$resourceKey"
            ];
        }
        return array_merge($collection, parent::collection($resourceKey, $data));
    }
}

// vim: syntax=php sw=4 ts=4 et:
