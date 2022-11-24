<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->default(false)->index();
            $table->string('name',150);
            $table->string('username',50)->unique()->index();
            $table->string('email')->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('status')->default(true);
            $table->string('api_token', 100)->nullable()->unique()->index();
            $table->tinyInteger('is_admin')->default(0);
            $table->rememberToken();
            $table->unsignedBigInteger('created_user_id')->default(0)->index();
            $table->unsignedBigInteger('updated_user_id')->default(0)->index();
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
        Schema::dropIfExists('users');
    }
}
