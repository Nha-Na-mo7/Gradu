<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoincheckPrice extends Model
{
    // JSONに含める属性
    // account_idはtwitter_accountsテーブルのものを参照するのでこちらでは不要
    protected $visible = [
        'brand_id',
        'price_min',
        'price_max',
        'updated_at'
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
