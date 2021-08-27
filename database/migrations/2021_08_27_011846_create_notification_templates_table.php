<?php

 use Illuminate\Database\Migrations\Migration;
 use Illuminate\Database\Schema\Blueprint;
 use Illuminate\Support\Facades\Schema;

 class CreateNotificationTemplatesTable extends Migration
 {
    /**
     * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')
                ->index()
                ->unique();
            $table->enum('type', ['push', 'email', 'sms', 'slack'])
                ->default('email');
            $table->string('lang')
                ->default('id');
            $table->json('required_data');
            $table->string('title');
            $table->string('template_path');
            $table->timestamp('created_at')
                ->useCurrent();
            $table->timestamp('updated_at')
                ->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('notification_templates');
    }
}