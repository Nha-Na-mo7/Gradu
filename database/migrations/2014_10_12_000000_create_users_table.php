<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('twitter_id')->nullable()->unique()->comment('連携したTwitterのID');
            $table->string('token')->nullable()->comment('連携アカウントのTwitterToken');
            $table->string('token_secret')->nullable()->comment('連携アカウントのTwitterTokenシークレット');
            $table->boolean('auto_follow_flg')->default(false)->comment('自動フォローがONであるか');
            $table->rememberToken();
            $table->timestamps();
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
