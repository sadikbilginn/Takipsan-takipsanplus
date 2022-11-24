<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->default(0)->index();
            $table->unsignedBigInteger('read_type_id')->default(0)->index();
            $table->string('device_type',50)->index();
            $table->string('name',100);
            $table->string('reader',50)->index();
            $table->string('reader_mode', 20)->nullable();
            $table->smallInteger('estimated_population')->default(0);
            $table->string('search_mode', 20)->nullable();
            $table->tinyInteger('session')->default(0);
            $table->text('string_set')->nullable();
            $table->string('gpio_start', 100)->nullable();
            $table->string('gpio_stop', 100)->nullable();
            $table->string('gpio_error', 100)->nullable();
            $table->string('printer_address',50)->nullable();
            $table->ipAddress('ip_address');
            $table->string('package_timeout',5);
            $table->boolean('common_power')->default(0);
            $table->text('antennas');
            $table->boolean('auto_print')->default(0);
            $table->boolean('auto_model_name')->default(0);
            $table->boolean('auto_size_name')->default(0);
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->unsignedBigInteger('updated_user_id')->default(0)->index();
            $table->boolean('status')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
