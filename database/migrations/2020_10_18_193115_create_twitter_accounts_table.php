<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_id')->unique()->comment('取得したTwitterアカウントのID');
            $table->string('name')->comment('アカウント名');
            $table->string('screen_name')->unique()->comment('@ から始まる英数字のユーザ名');
            $table->text('description')->nullable()->comment('プロフィール');
            $table->boolean('protected')->comment('非公開アカウントか否か');
            $table->integer('friends_count')->unsigned()->comment('フォロー数');
            $table->integer('followers_count')->unsigned()->comment('フォロワー数');
            $table->dateTime('account_created_at')->comment('そのアカウントが作成された日時');
            $table->string('profile_image_url_https')->nullable()->comment('アカウントのアイコンのURL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitter_accounts');
    }
}
