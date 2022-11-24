<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->default(0)->index();
            $table->unsignedBigInteger('order_id')->default(0)->index();
            $table->unsignedBigInteger('consignment_id')->default(0)->index();
            $table->integer('package_no')->default(0)->index();
            $table->string('size',20)->nullable();
            $table->string('model',50)->nullable();
            $table->unsignedBigInteger('order_model_id')->default(0)->index();
            $table->unsignedBigInteger('order_size_id')->default(0)->index();
            $table->unsignedBigInteger('device_id')->default(0)->index();
            $table->boolean('status')->default(0);
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('consignment_id')->references('id')->on('consignments')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
