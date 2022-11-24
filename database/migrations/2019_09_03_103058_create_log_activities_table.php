<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model',50);
            $table->string('subject',150);
            $table->string('subject_en',150);
            $table->string('url');
            $table->string('controller', 100);
            $table->string('action', 50);
            $table->string('method', 20);
            $table->ipAddress('ip');
            $table->string('agent')->nullable();
            $table->bigInteger('record_id')->default(0)->index();
            $table->bigInteger('user_id')->default(0)->index();
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_activities');
    }
}
