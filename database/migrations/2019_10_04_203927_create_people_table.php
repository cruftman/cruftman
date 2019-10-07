<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('personal_id', 100)->comment('Personal ID, such as Social Security ID');
            $table->string('first_name', 100)->nullable(false);
            $table->string('last_name', 100)->nullable(false);
            $table->date('birthday')->nullable();
            $table->enum('gender', ['male','female','bigender'])->nullable();
            $table->string('title', 20)->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // constraints & indexes
            $table->unique('personal_id');
            $table->index('first_name');
            $table->index('last_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}

// vim: syntax=php sw=4 ts=4 et:
