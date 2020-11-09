<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatedAtTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    // システム用のseeder、そのベースです。
    // アカウント検索や人気通貨ツイート検索などの自動処理におけるテーブルごとの最終更新日時を記録します。
    // このテーブルはユーザー側から触れることはできません。
    public function run()
    {
        DB::table('updated_at_tables')->insert([
            [
              // Twitterアカウント検索
                'table_name' => 'twitter_accounts',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
              // 仮想通貨ツイート数(1時間)
                'table_name' => 'trend_currency_1h',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
              // 仮想通貨ツイート数(24時間)
                'table_name' => 'trend_currency_24h',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
              // 仮想通貨ツイート数(7日間)
                'table_name' => 'trend_currency_7d',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
