<?php
/**
 * @file src/Cruftman/Http/Controllers/ModelController.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Dingo\Api\TransformerAbstract;

/**
 * Base class for Controller that serves instances of a particular Cruftman
 * Model.
 *
 * For groups of routes that serve particular model, it's enough to inherit from
 * this class as follows::
 *
 *      namespace Cruftman\Http\Controllers;
 *
 *      class PersonController extends Controller
 *      {
 *          protected $modelName = 'Person';
 *      }
 *
 * and the magick should do the rest.
 */
class ModelController extends Controller
{
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * Returns a collection of model instances for the $request.
     *
     * @param Request $request
     * @return Collection
     */
    public function fetchCollection(Request $request) : Collection
    {
        return call_user_func_array([$this->getModelClass(), 'all'], []);
    }

    /**
     * Returns a single model instance for the $request.
     *
     * @param Request $request
     * @param $id Instance identifier
     * @return Model|null
     */
    public function fetchInstance(Request $request, $id) : ?Model
    {
        return call_user_func_array([$this->getModelClass(), 'find'], [$id]);
    }

    /**
     * A controller method for '{resource}.index' route.
     */
    public function index(Request $request)
    {
        // Simply returning $collection would work in most cases, except for
        // the $empty collection. So, we must find $transformer by ourselves.

        $collection = $this->fetchCollection($request);
        $binding = $this->getTransformerBinding();
        if ($collection->isEmpty()) {
            $transformer = $binding->resolveTransformer();
            $parameters = $binding->getParameters();

            // The following call has side effect: the $collection's class
            // (Illuminate\Support\Collection::class) gets registered in
            // transformer's factory (app('api.transformer') - a singleton).
            // To minimize inpact, we let this to be made only for empty
            // collections, because any non-empty collections is handled
            // properly based on its first element, which should already be
            // registered.
            return $this->response->collection($collection, $transformer, $parameters);
        } else {
            return Response($collection, 200, [], $binding);
        }
    }

    /**
     * A controller method for '{resource}.show' route.
     */
    public function show(Request $request, $id)
    {
        $instance = $this->fetchInstance($request, $id);
        $binding = $this->getTransformerBinding();
        if ($instance === null) {
            return new Response(null, 200, [], $binding);
        }
//        if ($instance === null) {
//            return $this->response->errorNotFound(__('error.not_found')); 
//        }
        return Response($instance, 200, [], $binding);
        //return $this->response->item($instance, $binding->resolveTransformer(), $binding->getParameters());
    }

    protected function getTransformerBinding()
    {
        return app('api.transformer')->getTransformerBinding($this->getModelClass());
    }
}

// vim: syntax=php sw=4 ts=4 et:
