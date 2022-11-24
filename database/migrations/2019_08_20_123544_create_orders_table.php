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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consignee_id')->default(0)->index();
            $table->string('order_code', 20)->index();
            $table->string('po_no', 140)->index();
            $table->string('name', 200);
            $table->integer('item_count');
            $table->string('record_id', 50)->nullable();
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->unsignedBigInteger('updated_user_id')->default(0)->index();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
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
