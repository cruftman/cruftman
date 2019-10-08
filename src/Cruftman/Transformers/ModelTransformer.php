<?php
/**
 * @file src/Cruftman/Transformers/ModelTransformer.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

use League\Fractal\ParamBag;
use Cruftman\Models\ModelUserHelpers;

/**
 * Base class for Cruftman Model transformers .
 */
class ModelTransformer extends Transformer
{
    use ModelUserHelpers;

    public function transform(Model $model)
    {
        return $model->attributesToArray();
    }

    protected function applyCollectionLimits(Collection $collection, ParamBag $params) : Collection
    {
        $limit = $params->get('limit');
        if (is_array($limit) && count($limit) > 0) {
            if (count($limit) > 1 && ((string)(int)$limit[1] === (string)$limit[1])) {
                $collection = $collection->skip((int)$limit[1]);
            }
            if ((string)(int)$limit[0] === (string)$limit[0]) {
                $collection = $collection->take((int)$limit[0]);
            }
        }
        return $collection;
    }

    protected function applyCollectionSorting(Collection $collection, ParamBag $params) : Collection
    {
        $order = $params->get('order');
        if (is_array($order) && count($order) > 0) {
            if (count($order) > 1 && strtolower($order[1]) == 'desc') {
                $collection = $collection->sortByDesc($order[0]);
            } else {
                $collection = $collection->sortBy($order[0]);
            }
        }
        return $collection;
    }

    protected function applyCollectionParams(Collection $collection, ParamBag $params) : Collection
    {
        $collection = $this->applyCollectionLimits($collection, $params);
        $collection = $this->applyCollectionSorting($collection, $params);
        return $collection;
    }

    protected function resolveRelatedTransformer(Relation $relation)
    {
        $factory = app(\Dingo\Api\Transformer\Factory::class);
        $related = $relation->getRelated();
        if (!$factory->transformableResponse($related)) {
            throw new \RuntimeException('failed to find transformer');
        }
        $bindings = $factory->getTransformerBindings();
        $binding = $bindings[get_class($related)];
        return $binding->resolveTransformer();
    }

    protected function transformRelatedCollection(Relation $relation, ?ParamBag $params = null, $transformer = null)
    {
        if (!isset($transformer)) {
            $transformer = $this->resolveRelatedTransformer($relation);
        }
        return $this->transformCollection($relation->get(), $transformer, $params);
    }

    protected function transformCollection(Collection $collection, $transformer, ?ParamBag $params = null)
    {
        if (isset($params)) {
            $collection = $this->applyCollectionParams($collection, $params);
        }
        return $this->collection($collection, $transformer);
    }
}

// vim: syntax=php sw=4 ts=4 et:
