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

$api->get('/people', [
    'uses' => 'PersonController@index',
    'as' => 'person.index'
]);

$api->get('/person/{id}', [
    'uses' => 'PersonController@show',
    'as' => 'person.show'
]);

$api->get('/locations', [
    'uses' => 'LocationController@index',
    'as' => 'location.index'
]);

$api->get('/location/{id}', [
    'uses' => 'LocationController@show',
    'as' => 'location.show'
]);

// vim: syntax=php sw=4 ts=4 et:
