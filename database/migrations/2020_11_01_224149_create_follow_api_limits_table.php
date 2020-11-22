<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowApiLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_api_limits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_id')->nullable()->unique()->comment('usersテーブルのtwitter_idと紐付けるアカウントID');
            $table->integer('day_follow_count')->nullable()->default(0)->unsigned()->comment('アカウントが1日の間にフォローした回数');
            $table->timestamp('day_follow_limit_time')->nullable()->comment('1日のフォロー制限が解除される日時(この時間内かつ制限回数オーバーの時、リクエストを発火させない)');
            $table->integer('fifteen_min_follow_count')->nullable()->default(0)->unsigned()->comment('アカウントが15分の間にフォローした回数');
            $table->timestamp('fifteen_min_follow_limit_time')->nullable()->comment('15分以内のフォロー制限が解除される日時(この時間内かつ制限回数オーバーの時、リクエストを発火させない)');
    
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
        Schema::dropIfExists('follow_api_limits');
    }
}
