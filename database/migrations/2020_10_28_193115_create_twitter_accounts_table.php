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
            $table->bigIncrements('id'); // プライマリーキー
            $table->string('account_id')->unique(); // 取得したTwitterアカウントのID
            $table->string('name'); // アカウント名
            $table->string('screen_name')->unique(); // @から始まるユーザーネーム
            $table->text('description')->nullable(); // プロフィール
            $table->boolean('protected'); // 鍵アカウントであるか
            $table->integer('friends_count')->unsigned(); // フォロー数
            $table->integer('followers_count')->unsigned(); // フォロワー数
            $table->dateTime('account_created_at'); // アカウントが作られた日時
            // $table->string('status_id_str')->unique()->nullable(); // 最新ツイートのID
            // $table->text('status_text')->nullable(); // 最新ツイートの内容
            // $table->timestamp('status_created_at')->nullable(); // 最新ツイートの投稿時刻
            $table->string('profile_image_url_https')->nullable(); // 画像アイコンのURL
            // $table->boolean('following'); // 連携済みユーザーがこのユーザーをフォローしているか
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
