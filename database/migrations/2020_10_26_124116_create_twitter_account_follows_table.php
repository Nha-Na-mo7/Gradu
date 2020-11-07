<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterAccountFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_account_follows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_id'); // 紐付けるアカウントID
            $table->string('follow_target_id'); // フォロー先のアカウントのID
          
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
        Schema::dropIfExists('twitter_account_follows');
    }
}
