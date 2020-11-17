<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TweetCountDaysTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for ($i = 7;$i > 0;$i--){
        DB::table('tweet_count_days')->insert([
            [
                'brand_id' => 1,
                'tweet_count' => random_int(4000,6000),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 2,
                'tweet_count' => random_int(600,900),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 3,
                'tweet_count' => random_int(5000,7000),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 4,
                'tweet_count' => random_int(9500,18000),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 5,
                'tweet_count' => random_int(100,400),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 6,
                'tweet_count' => random_int(800,1000),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 7,
                'tweet_count' => random_int(800,2000),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 8,
                'tweet_count' => random_int(50,200),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 9,
                'tweet_count' => random_int(100,400),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 10,
                'tweet_count' => random_int(1000,2000),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 11,
                'tweet_count' => random_int(10,50),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 12,
                'tweet_count' => random_int(5,20),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 13,
                'tweet_count' => random_int(300,800),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 14,
                'tweet_count' => random_int(10,30),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 15,
                'tweet_count' => random_int(6000,8000),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 16,
                'tweet_count' => random_int(10,50),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 17,
                'tweet_count' => random_int(20,80),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
            [
                'brand_id' => 18,
                'tweet_count' => random_int(500,1000),
                'complete_flg' => true,
                'next_results' => NULL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::today()->subDays($i - 1),
            ],
        ]);
  
      }
    }
}
