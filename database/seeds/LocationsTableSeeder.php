<?php

use Illuminate\Database\Seeder;
use Cruftman\Models\Location;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Location::class, 10)->create();
    }
}
