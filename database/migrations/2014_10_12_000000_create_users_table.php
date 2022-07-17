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
            $table->bigInteger('id', true, true);
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->string('phone')->unique();
            $table->tinyText('country')
                ->nullable();
            $table->boolean('is_email_verified')
                ->default(false);
            $table->boolean('is_phone_verified')
                ->default(false);
            $table->rememberToken();
            $table->timestamps();

            // defining foreign key here!
            // code...
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
