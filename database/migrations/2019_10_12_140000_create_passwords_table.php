<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passwords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identifier', 256)->nullable(false);
            $table->string('password', 256)->nullable(false);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('disabled')->default(false);
            $table->timestamps();
        });

        Schema::table('passwords', function (Blueprint $table) {
            $table->unique(['identifier']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passwords');
    }
}
