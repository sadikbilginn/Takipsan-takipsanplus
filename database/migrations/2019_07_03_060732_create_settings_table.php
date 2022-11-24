<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('group_key', 50)->nullable();
            $table->tinyInteger('required')->default(0);
            $table->string('area_type', 50)->nullable();
            $table->string('title', 100)->nullable();
            $table->string('description')->nullable();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->tinyInteger('locale')->default(0);
            $table->tinyInteger('sort')->default(0);
            $table->timestamps();
            $table->index(['group_key', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
