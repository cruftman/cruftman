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
    $relations->hasMany('occupied-locations', 'locations');
    $relations->hasMany('users');
});
$api->resource('locations')->relationships(function ($relations) {
    $relations->hasMany('occupants', 'people');
});
$api->resource('users')->relationships(function ($relations) {
    $relations->hasOne('person');
    $relations->hasOne('password');
    $relations->hasMany('accounts');
});
$api->resource('passwords')->relationships(function ($relations) {
    $relations->hasOne('user');
});
$api->resource('accounts')->relationships(function ($relations) {
    $relations->hasMany('users');
});
