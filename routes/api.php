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

$api->resource('people', 'PersonController', [
    'transform' => [\Cruftman\Models\Person::class => \Cruftman\Transformers\PersonTransformer::class]
]);
$api->resource('person.occupied_locations', 'LocationController');
$api->resource('locations', 'LocationController', [
    'transform' => [\Cruftman\Models\Location::class => \Cruftman\Transformers\LocationTransformer::class]
]);

// vim: syntax=php sw=4 ts=4 et:
