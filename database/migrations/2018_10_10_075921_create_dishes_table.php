<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->text('description');
            $table->unsignedInteger('dish_category_id');
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('user_id');
            $table->string('image')->nullable();
            $table->boolean('is_in_stock')->default(true);
            $table->timestamps();

            $table->foreign('dish_category_id')->references('id')->on('dish_categories');
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
        Schema::dropIfExists('dishes');
    }
}
