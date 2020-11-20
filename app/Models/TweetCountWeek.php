<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TweetCountWeek extends Model
{
  
    public $timestamps = false;
  
    // JSONに含める属性
    // フロントエンドで不必要な情報は除外する
    protected $visible = [
        'brand_id',
        'tweet_count',
        'updated_at',
        'brand'
    ];
    
    // fillable
    protected $fillable = [
        'brand_id',
        'tweet_count',
        'updated_at',
        'complete_flg',
        'next_results',
    ];
    /**
     * リレーション - brandsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
      return $this->belongsTo('App\Models\Brand');
    }
}
