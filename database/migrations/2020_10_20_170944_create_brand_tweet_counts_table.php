<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandTweetCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      
      // TODO 削除するかもしれません
      
        Schema::create('brand_tweet_counts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('brand_id')->unsigned()->comment('通貨ID');
            $table->integer('tweet_count')->unsigned()->comment('取得時点でのツイート数');
            $table->integer('tweet_count_before')->unsigned()->comment('直前のツイート数');
            $table->integer('interval_hour')->unsigned()->comment('ツイート取得間隔・1時間単位');
            $table->timestamps();
    
            //外部キーでtwitter_accountsのaccount_idと紐付け。
            //主テーブルのレコードが削除されたら、このテーブルのデータも一緒に消える。
            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
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
        Schema::dropIfExists('brand_tweet_counts');
    }
}
