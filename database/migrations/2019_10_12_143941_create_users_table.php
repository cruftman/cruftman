<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
//            $table->string('objectguid')->nullable();
//            $table->unsignedBigInteger('password_id')->nullable();
            $table->unsignedBigInteger('person_id')->nullable();
//            $table->string('name', 256)->nullable();
            $table->string('username', 256)->nullable(false);
            $table->string('password', 256);
//            $table->boolean('enabled')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
//            $table->unique('objectguid');
            /*
            $table->unique('password_id');

            $table->foreign('password_id')
                  ->references('id')
                  ->on('passwords')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
             */
            $table->foreign('person_id')
                  ->references('id')
                  ->on('people')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
