<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoincheckPrice extends Model
{
    // JSONに含める属性
    // フロント側で不要な属性は含めない
    protected $visible = [
        'brand_id',
        'price_min',
        'price_max',
        'updated_at',
        'brand'
    ];
    
    // fillable
    protected $fillable = [
        'brand_id',
        'price_min',
        'price_max',
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
