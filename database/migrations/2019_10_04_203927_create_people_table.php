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
            $table->string('uid', 100);
            $table->string('givenname', 100)->nullable(false);
            $table->string('surname', 100)->nullable(false);
            $table->string('nickname', 100)->nullable();
            $table->date('birthday')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // constraints & indexes
            $table->unique('uid');
            $table->index('givenname');
            $table->index('surname');
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
