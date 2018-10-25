<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->decimal('amount', 8, 2);
            $table->decimal('change', 8, 2);
            $table->unsignedInteger('payment_method_id');
            $table->string('credit_card_number')->nullable();
            $table->string('credit_card_expiration_year')->nullable();
            $table->string('credit_card_expiration_month')->nullable();
            $table->string('credit_card_cvc')->nullable();
            $table->unsignedInteger('gift_card_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('gift_card_id')->references('id')->on('gift_cards');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
