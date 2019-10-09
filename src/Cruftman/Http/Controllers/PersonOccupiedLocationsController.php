<?php
/**
 * @file src/Cruftman/Http/Controllers/PersonOccupiedLocationsController.php
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
class PersonOccupiedLocationsController extends NestedModelController
{
    protected $modelClass = \Cruftman\Models\Person::class;
    protected $relationName = 'occupied_locations';
}

// vim: syntax=php sw=4 ts=4 et:
