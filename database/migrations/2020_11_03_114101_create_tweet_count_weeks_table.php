<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTweetCountWeeksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweet_count_weeks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('brand_id')->unsigned()->comment('通貨ID');
            $table->integer('tweet_count')->unsigned()->comment('取得時点でのツイート数');
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
        Schema::dropIfExists('tweet_count_weeks');
    }
}