<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->default(0)->index();
            $table->unsignedBigInteger('order_id')->default(0)->index();
            $table->unsignedBigInteger('consignment_id')->default(0)->index();
            $table->unsignedBigInteger('package_id')->default(0)->index();
            $table->string('epc', 64)->index();
            $table->string('size')->nullable();
            $table->unsignedBigInteger('device_id')->default(0)->index();
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
