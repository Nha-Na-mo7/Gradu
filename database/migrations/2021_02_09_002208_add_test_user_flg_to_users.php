<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTestUserFlgToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // テストユーザーかを判定するためのカラム。基本的にfalse。
        Schema::table('users', function (Blueprint $table) {
          $table->boolean('test_user_flg')->default(false)->after('auto_follow_flg')->comment('テストユーザーであるか');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
          $table->dropColumn('test_user_flg');
        });
    }
}
