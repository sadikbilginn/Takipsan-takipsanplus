<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsigneesToSayfaGorunum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignees', function (Blueprint $table) {
            $table->enum('sayfa_gorunum',['Normal','CSV','PDF','Ozel'])->default('Normal');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consignees', function (Blueprint $table) {
            $table->dropColumn('sayfa_gorunum');
        });
    }
}
