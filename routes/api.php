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

$api->resource('person', 'PersonController');
$api->resource('person.occupied_locations', 'LocationController'); #!!!
$api->resource('location', 'LocationController');

// vim: syntax=php sw=4 ts=4 et:
