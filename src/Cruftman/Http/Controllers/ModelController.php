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
        return $this->fetchCollection($request);
    }

    /**
     * A controller method for '{resource}.show' route.
     */
    public function show(Request $request, $id)
    {
        $instance = $this->fetchInstance($request, $id);
        if ($instance == null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }
        return $instance;
    }
}

// vim: syntax=php sw=4 ts=4 et:
