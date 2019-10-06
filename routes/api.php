<?php

/*
|--------------------------------------------------------------------------
| V1 API routes
|--------------------------------------------------------------------------
*/

/**
 * @OA\Get(
 *      path="/",
 *      summary="Display welcome message",
 *      @OA\Response(
 *          response=200,
 *          description="OK"
 *      )
 * )
 */
$api->get('/', function () {
    return [
        'message' => __('messages.welcome'),
        'branch' => 'dev-master'
    ];
});

$api->get('/persons', [
    'uses' => 'PersonController@index',
    'as' => 'getPersons'
]);

$api->get('/person/{id}', [
    'uses' => 'PersonController@show',
    'as' => 'getPersonById'
]);

$api->get('/location/{id}', [
    'uses' => 'LocationController@show',
    'as' => 'getLocationById'
]);

// vim: syntax=php sw=4 ts=4 et:
