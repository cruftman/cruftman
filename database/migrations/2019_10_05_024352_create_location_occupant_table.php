<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationOccupantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_occupant', function (Blueprint $table) {
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('location_id');
            $table->timestamps();

            // constraints & indices
            $table->unique(['person_id', 'location_id']);
        });

        Schema::table('location_occupant', function(Blueprint $table) {
            $table->foreign('person_id')
                  ->references('id')
                  ->on('people')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreign('location_id')
                  ->references('id')
                  ->on('locations')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_occupant');
    }
}

// vim: syntax=php sw=4 ts=4 et:
