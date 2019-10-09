<?php
/**
 * @file src/Cruftman/Transformers/Transformer.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Transformers;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;

use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Contracts\Paginator\Paginator as IlluminatePaginator;

class Transformer extends TransformerAbstract
{
    public function createFractalResource($data, $transformer = null, $resourceKey = null)
    {
        if (!isset($transformer)) {
            $binding = app('api.transformer')->getTransformerBinding($data);
            $transformer = $binding->resolveTransformer();
            $parameters = $binding->getParameters();
            if (!isset($resourceKey)) {
                $resourceKey = $parameters['key'] ?? null;
            }
        }

        // Here we actually repeat Dingo\Api\Transformer\Adapter\Fractal::createResource()
        // which is not protected.
        if ($data instanceof IlluminatePaginator || $data instanceof IlluminateCollection) {
            return new FractalCollection($data, $transformer, $resourceKey);
        }
        return new FractalItem($data, $transformer, $resourceKey);
    }
}

// vim: syntax=php sw=4 ts=4 et:
