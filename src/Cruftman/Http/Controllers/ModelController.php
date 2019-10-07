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

use Cruftman\Models\ModelUserHelpers;
use Cruftman\Transformers\TransformerUserHelpers;

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
    use ModelUserHelpers;
    use TransformerUserHelpers;

    /**
     * Returns an array of additional arguments passed to JsonApiSerializer.
     *
     * @returns array
     */
    public function getResourceParams() : array
    {
        return ['key' => $this->getModelResourceKey()];
    }

    /**
     * Calls appropriate 'constructor' for the response, such as $this->response->collection(...).
     *
     * @param string $constructor name of the constructor function
     * @param mixed $result the result to be morphed into HTTP response
     * @param TransformerAbstract|null $transformer
     */
    protected function getModelResponse(
        string $constructor,
        $result,
        ?TransformerAbstract $transformer = null,
        array $params = []
    ) : Response {
        $args = [$result, $transformer ?? $this->getTransformer(), array_merge($this->getResourceParams(), $params)];
        return call_user_func_array([$this->response, $constructor], $args);
    }

    /**
     * Return HTTP response for collective result.
     *
     * @return Response
     */
    public function getModelCollectionResponse(
        Collection $result,
        ?TransformerAbstract $transformer = null,
        array $params = []
    ) : Response {
        return $this->getModelResponse('collection', $result, $transformer, $params);
    }

    /**
     * Return HTTP response for a single-item result.
     *
     * @return Response
     */
    public function getModelItemResponse(
        Model $result,
        ?TransformerAbstract $transformer = null,
        array $params = []
    ) : Response {
        return $this->getModelResponse('item', $result, $transformer, $params);
    }

    /**
     * Returns a collection of model instances for the $request.
     *
     * @param Request $request
     * @return Collection
     */
    public function getModelInstances(Request $request) : Collection
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
    public function getModelInstance(Request $request, $id) : ?Model
    {
        return call_user_func_array([$this->getModelClass(), 'find'], [$id]);
    }

    /**
     * A controller method for '{resource}.index' route.
     */
    public function index(Request $request)
    {
        $models = $this->getModelInstances($request);
        return $this->getModelCollectionResponse($models);
    }

    /**
     * A controller method for '{resource}.show' route.
     */
    public function show(Request $request, $id)
    {
        $model = $this->getModelInstance($request, $id);
        if ($model == null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }
        return $this->getModelItemResponse($model);
    }
}

// vim: syntax=php sw=4 ts=4 et:
