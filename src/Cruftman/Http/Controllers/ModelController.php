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

use Illuminate\Http\Request;

/**
 * Base class for Controller that serves instances of Eloquent models.
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
     * @param  Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveCollection(Request $request)
    {
        return call_user_func_array([$this->getModelClass(), 'all'], []);
    }

    /**
     * Returns a single model instance for the $request.
     *
     * @param  Request $request
     * @param  $id Instance identifier
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function retrieveInstance(Request $request, $id)
    {
        return call_user_func_array([$this->getModelClass(), 'find'], [$id]);
    }

    /**
     * A controller method for '{resource}.index' route.
     */
    public function index(Request $request)
    {
        return $this->retrieveCollection($request);
    }

    /**
     * A controller method for '{resource}.show' route.
     */
    public function show(Request $request, $id)
    {
        return $this->retrieveInstance($request, $id);
    }
}

// vim: syntax=php sw=4 ts=4 et:
