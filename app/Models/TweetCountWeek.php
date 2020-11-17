<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TweetCountWeek extends Model
{
  
    public $timestamps = false;
  
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
