<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('read_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100);
            $table->string('name_en',100);
            $table->string('reader',20)->index();
            $table->string('reader_mode', 20)->nullable();
            $table->smallInteger('estimated_population')->default(0);
            $table->string('search_mode', 20)->nullable();
            $table->tinyInteger('session')->default(0);
            $table->text('string_set')->nullable();
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->unsignedBigInteger('updated_user_id')->default(0)->index();
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
        Schema::dropIfExists('read_types');
    }
}
