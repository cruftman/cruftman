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
        return $model->toArray();
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

    protected function transformRelated(Collection $collection, ModelTransformer $transformer, ?ParamBag $params)
    {
        if(!is_null($params)) {
            $collection = $this->applyCollectionParams($collection, $params);
        }
        return $this->collection($collection, $transformer, $transformer->getModelResourceKey());
    }
}

// vim: syntax=php sw=4 ts=4 et:
