<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterAccountNewTweetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_account_new_tweets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('account_id')->unique(); // ツイート主のアカウントID
            $table->bigInteger('tweet_id_str')->unique()->nullable(); // ツイートのID
            $table->text('tweet_text')->nullable(); // ツイート内容
            $table->dateTime('tweet_created_at')->nullable(); //ツイート日時
  
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
        Schema::dropIfExists('twitter_account_new_tweets');
    }
}
