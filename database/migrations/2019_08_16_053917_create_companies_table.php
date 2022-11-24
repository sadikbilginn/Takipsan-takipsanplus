<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',150);
            $table->string('title',255);
            $table->string('phone',25);
            $table->string('email',255);
            $table->string('address',255);
            $table->string('logo')->nullable();
            $table->string('latitude', 20)->nullable();
            $table->string('longitude', 20)->nullable();
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->unsignedBigInteger('updated_user_id')->default(0)->index();
            $table->integer('old_id')->default(0)->index();
            $table->string('record_id', 50)->nullable();
            $table->boolean('status')->default(0)->index();
            $table->boolean('consignment_close')->default(false);
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
        Schema::dropIfExists('companies');
    }
}
