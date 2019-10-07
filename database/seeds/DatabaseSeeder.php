<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PeopleTableSeeder::class,
            LocationsTableSeeder::class,
            LocationOccupantTableSeeder::class
        ]);
    }
}

// vim: syntax=php sw=4 ts=4 et:
