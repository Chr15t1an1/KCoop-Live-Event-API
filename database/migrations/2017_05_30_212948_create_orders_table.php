<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {

            // Identifyers
            $table->increments('id');

            $table->string('email')->nullable();
            $table->string('first_name')->nullable();

            $table->string('shopify_order_id');

            ///Relationships
            $table->string('event_id');

            //Compleatness of the order registation
            $table->boolean('tickets_created');
            $table->integer('num_tickets');
            $table->integer('num_tickets_claimed');
            $table->boolean('registration_compleate');

            // secret_key for the email link
            $table->string('secret_key')->unique();

            //Orig post request
            $table->longText('order_object');
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
        Schema::dropIfExists('orders');
    }
}
