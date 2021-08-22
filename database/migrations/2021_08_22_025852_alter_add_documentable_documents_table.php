<?php

 use Illuminate\Database\Migrations\Migration;
 use Illuminate\Database\Schema\Blueprint;
 use Illuminate\Support\Facades\Schema;

 class AlterAddDocumentableDocumentsTable extends Migration
 {
     /**
      * Run the migrations.
      *
      * @return void
      */
     public function up()
     {
         Schema::table('documents', function (Blueprint $table) {
             $table->unsignedInteger("table_id")->after('id');
             $table->string("table")
                 ->nullable()
                 ->after('table_id');
             $table->index(["table_id", "table"], 'documentable_index');
         });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::table('documents', function (Blueprint $table) {
             $table->dropIndex('documentable_index');
             $table->dropColumn('table');
             $table->dropColumn('table_id');
         });
     }
 }