<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_targets', function (Blueprint $table) {
          
          $table->bigIncrements('id');
          $table->string('account_id')->nullable()->comment('紐付いているアカウントID');
          $table->string('follow_target_id')->comment('フォロー先のアカウントID');
      
          //外部キーでusersのtwitter_idと紐付け。
          $table->foreign('account_id')
              ->references('twitter_id')
              ->on('users')
              ->onDelete('cascade')
              ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow_targets');
    }
}
