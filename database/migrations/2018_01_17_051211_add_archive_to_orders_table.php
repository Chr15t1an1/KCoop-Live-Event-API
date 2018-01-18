<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArchiveToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('orders', function($table) {
          $table->integer('is_archived')->nullable();
        });

      Schema::table('tickets', function($table) {
            $table->integer('is_archived')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function($table) {
          $table->dropColumn('is_archived');
      });

      Schema::table('tickets', function($table) {
        $table->dropColumn('is_archived');
      });

    }
}
