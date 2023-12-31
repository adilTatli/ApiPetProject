<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            $table->integer('permission_id')->unsigned();
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->increments('id');

            $table->biginteger('user_id')->unsigned()->nullable();
            $table->integer('role_id')->unsigned()->nullable();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
    }
}
