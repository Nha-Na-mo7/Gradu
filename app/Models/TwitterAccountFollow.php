<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterAccountFollow extends Model
{
  // 1つのtwitterアカウントがフォローしているアカウントIDのテーブルです
  // twitter_accountsテーブルに紐づきます(1:多)
  
  public $timestamps = false;
  
  protected $fillable = [
      'account_id',
      'follow_target_id'
  ];
  
  /**
   * リレーション - twitter_accountsテーブル
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function owner()
  {
    return $this->belongsTo('App\Models\TwitterAccount', 'account_id', 'account_id', 'twitter_accounts');
  }
  
}
