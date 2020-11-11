<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterAccountApiLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_account_api_limits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_id')->comment('紐付けるアカウントID');
            $table->integer('day_follow_count')->nullable()->default(0)->unsigned()->comment('アカウントが1日の間にフォローした回数');
            $table->timestamp('day_follow_limit_time')->nullable()->comment('1日のフォロー制限が解除される日時(この時間内かつ制限回数オーバーの時、リクエストを発火させない)');
            $table->integer('fifteen_min_follow_count')->nullable()->default(0)->unsigned()->comment('アカウントが15分の間にフォローした回数');
            $table->timestamp('fifteen_min_follow_limit_time')->nullable()->comment('15分以内のフォロー制限が解除される日時(この時間内かつ制限回数オーバーの時、リクエストを発火させない)');
            
            //外部キーでtwitter_accountsのaccount_idと紐付け。
            //主テーブルのレコードが削除されたら、このテーブルのデータも一緒に消える。
            $table->foreign('account_id')
                ->references('account_id')
                ->on('twitter_accounts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitter_account_api_limits');
    }
}
