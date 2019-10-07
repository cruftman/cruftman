<?php

$factory->define(Cruftman\Models\Location::class, function (Faker\Generator $faker) {
    return [
        'name' => (string)$faker->numberBetween(100, 200),
        'comment' => $faker->sentence
    ];
});

// vim: syntax=php sw=4 ts=4 et:
