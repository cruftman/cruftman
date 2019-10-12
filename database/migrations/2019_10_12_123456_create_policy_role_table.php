<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolicyRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_role', function (Blueprint $table) {
            $table->unsignedBigInteger('policy_id');
            $table->unsignedBigInteger('role_id');
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            // constraints & indices
            $table->primary(['policy_id', 'role_id']);
        });

        Schema::table('policy_role', function(Blueprint $table) {
            $table->foreign('policy_id')
                  ->references('id')
                  ->on('policies')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
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
        Schema::dropIfExists('policy_role');
    }
}
