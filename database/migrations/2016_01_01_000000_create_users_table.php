<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('User', function (Blueprint $table) {
            $table->increments('UserID');
            $table->string('Name');
            $table->string('Email')->unique();
            $table->string('Password');
            $table->string('RememberToken', 100)->nullable();
            $table->timestamp('CreatedAt', 7)->nullable();
            $table->timestamp('UpdatedAt', 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('User');
    }
}
