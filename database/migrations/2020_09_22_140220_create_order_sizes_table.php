<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_sizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->default(0)->index();
            $table->unsignedBigInteger('order_id')->default(0)->index();
            $table->unsignedBigInteger('consignment_id')->default(0)->index();
            $table->unsignedBigInteger('order_model_id')->default(0)->index();
            $table->string('name',150);
            $table->integer('quantity')->default(0);
            $table->string('record_id', 50)->nullable();
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->unsignedBigInteger('updated_user_id')->default(0)->index();
            $table->timestamps();
            $table->foreign('order_model_id')->references('id')->on('order_models')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_sizes');
    }
}
