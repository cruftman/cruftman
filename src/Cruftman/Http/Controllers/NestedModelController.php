<?php
/**
 * @file src/Cruftman/Http/Controllers/NestedModelController.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Http\Controllers;

use Dingo\Api\Http\Request;

class NestedModelController extends Controller
{
    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function getRelationName()
    {
        return $this->relationName;
    }

    public function fetchInstance(Request $request, $parent_id)
    {
        return call_user_func_array([$this->getModelClass(), 'find'], [$parent_id]);
    }

    public function fetchNested(Request $request, $parent_id, $id = null)
    {
        $parent = $this->fetchInstance($request, $parent_id);
        if ($parent === null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }

        $nested = $parent->{$this->getRelationName()};
        if ($nested == null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }
        if ($id === null) {
            return $nested;
        } else {
            return call_user_func_array([$nested, 'find'], [$id]);
        }
    }

    /**
     * A controller method for '{resource}.index' route.
     */
    public function index(Request $request, $parent_id)
    {
        return $this->fetchNested($request, $parent_id);
    }

    /**
     * A controller method for '{resource}.show' route.
     */
    public function show(Request $request, $parent_id, $id)
    {
        $nested = $this->fetchNested($request, $parent_id, $id);
        if ($nested === null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }
        return $nested;
    }
}

// vim: syntax=php sw=4 ts=4 et:
