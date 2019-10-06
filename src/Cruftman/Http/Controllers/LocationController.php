<?php
/**
 * @file src/Cruftman/Http/Controllers/LocationController.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Http\Controllers;

use Cruftman\Models\Location;
use Cruftman\Transformers\LocationTransformer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Location controller.
 */
class LocationController extends Controller
{
    public function show($id)
    {
        if (($location = Location::find($id)) == null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }
        return $this->response->item($location, new LocationTransformer, ['key' => 'location']);
    }
}

// vim: syntax=php sw=4 ts=4 et:
