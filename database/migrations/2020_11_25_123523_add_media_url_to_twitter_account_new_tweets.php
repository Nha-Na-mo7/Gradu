<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaUrlToTwitterAccountNewTweets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twitter_account_new_tweets', function (Blueprint $table) {
          $table->string('media_url')->nullable()->comment('ツイートの画像URL。サイズはフロント側で":thumb"などを付与して調整');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twitter_account_new_tweets', function (Blueprint $table) {
          $table->dropColumn('media_url');
        });
    }
}
