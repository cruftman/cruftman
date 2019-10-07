<?php

use Illuminate\Database\Seeder;
use Cruftman\Models\Location;
use Cruftman\Models\Person;

class LocationOccupantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $people = Person::all();
        Location::all()->each(function ($location) use ($people) {
            $location->occupants()->attach(
                $people->random(rand(0,3))->pluck('id')->toArray()
            );
        });
    }
}
