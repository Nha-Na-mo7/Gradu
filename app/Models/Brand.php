<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    // タイムスタンプカラムは用意していないので、無理やり挿入しようとしてエラーにならないようにfalseにする
    public $timestamps = false;
  
  
  
    /**
     * リレーション - tweet_count_hours テーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tweet_count_hours()
    {
      return $this->hasMany('App\Models\TweetCountHour');
    }
    
    /**
     * リレーション - tweet_count_days テーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tweet_count_days()
    {
      return $this->hasMany('App\Models\TweetCountDay');
    }

    
    /**
     * リレーション - tweet_count_weeks テーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tweet_count_weeks()
    {
      return $this->hasMany('App\Models\TweetCountWeek');
    }
    
    /**
     * リレーション - coincheck_prices テーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coincheck_prices()
    {
      return $this->hasMany('App\Models\CoincheckPrice');
    }
}
