<?php
/**
 * @file src/Cruftman/Http/Controllers/LocationOccupantsController.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Http\Controllers;

/**
 * Person controller.
 */
class LocationOccupantsController extends NestedModelController
{
    protected $modelClass = \Cruftman\Models\Locations::class;
    protected $relationName = 'occupants';
}

// vim: syntax=php sw=4 ts=4 et:
