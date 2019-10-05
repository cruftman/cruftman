<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person_location', function (Blueprint $table) {
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('location_id');
            $table->timestamps();

            // constraints & indices
            $table->unique(['person_id', 'location_id']);
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_location');
    }
}
