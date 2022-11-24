<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->default(0)->index();
            $table->unsignedBigInteger('company_id')->default(0)->index();
            $table->unsignedBigInteger('consignee_id')->default(0)->index();
            $table->string('name',150);
            $table->string('plate_no',20)->nullable();
            $table->integer('item_count');
            $table->date('delivery_date');
            $table->string('record_id', 50)->nullable();
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->unsignedBigInteger('updated_user_id')->default(0)->index();
            $table->integer('old_id')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consignments');
    }
}
