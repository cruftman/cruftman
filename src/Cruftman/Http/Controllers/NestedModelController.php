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
    public function getRootItemClass()
    {
        return $this->rootModelClass;
    }

    public function getRelatedModelClass()
    {
        return $this->relatedModelClass;
    }

    public function getRelationName()
    {
        return $this->relationName;
    }

    public function getRootItem(Request $request, $root_id)
    {
        return call_user_func_array([$this->getRootItemClass(), 'find'], [$root_id]);
    }

    public function getRelated(Request $request, $root_id, $id = null)
    {
        $root = $this->getRootItem($request, $root_id);
        if ($root === null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }

        $related = $root->{$this->getRelationName()};
        if ($related == null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }
        if ($id === null) {
            return $related;
        } else {
            return call_user_func_array([$related, 'find'], [$id]);
        }
    }

    /**
     * A controller method for '{resource}.index' route.
     */
    public function index(Request $request, $root_id)
    {
        return $this->getRelated($request, $root_id);
    }

    /**
     * A controller method for '{resource}.show' route.
     */
    public function show(Request $request, $root_id, $id)
    {
        $related = $this->getRelated($request, $root_id, $id);
        if ($related === null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }
        return $related;
    }
}

// vim: syntax=php sw=4 ts=4 et:
