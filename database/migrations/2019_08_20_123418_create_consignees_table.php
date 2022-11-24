<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',150);
            $table->string('logo',100);
            $table->string('phone',20)->nullable();
            $table->string('address')->nullable();
            $table->string('auth_name', 100)->nullable();
            $table->string('auth_phone',20)->nullable();
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->unsignedBigInteger('updated_user_id')->default(0)->index();
            $table->integer('old_id')->default(0)->index();
            $table->string('record_id', 50)->nullable();
            $table->boolean('status')->default(0)->index();
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
        Schema::dropIfExists('consignees');
    }
}
