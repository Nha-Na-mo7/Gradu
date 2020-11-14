<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoincheckPrice extends Model
{
    //
  
  
    /**
     * リレーション - brandsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
      return $this->belongsTo('App\Models\Brand');
    }
}
