<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailAndCompanyToTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
       {
           Schema::table('tickets', function($table) {
           $table->string('email')->nullable();
           $table->string('company')->nullable();
           
           });
       }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('tickets', function($table) {
        $table->dropColumn('email');
        $table->dropColumn('company');
    });
    }
}

