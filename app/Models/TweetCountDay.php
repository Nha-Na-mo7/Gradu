<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TweetCountDay extends Model
{
    // fillable
    protected $fillable = [
        'brand_id',
        'tweet_count',
        'updated_at',
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
