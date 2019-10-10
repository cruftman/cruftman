<?php

/*
|--------------------------------------------------------------------------
| Json API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api->resource('people')->relationships(function ($relations) {
    $relations->hasMany('occupied_locations', 'locations');
});
$api->resource('locations')->relationships(function ($relations) {
    $relations->hasMany('occupants', 'people');
});
