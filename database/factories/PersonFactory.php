<?php

$factory->define(Cruftman\Models\Person::class, function (Faker\Generator $faker) {
    $gender = $faker->randomElement(['male', 'female', null]);
    return [
        'personal_id' => $faker->ssn,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'birthday' => $faker->date('Y-m-d', 'now'),
        'gender' => $gender,
        'title' => $faker->title($gender),
        'comment' => $faker->sentence
    ];
});

// vim: syntax=php sw=4 ts=4 et:
