<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterAccountNewTweet extends Model
{
  public $timestamps = false;
  
  // 1ページに表示させる数
  protected $perPage = 20;
  
  // JSONに含める属性
  // account_idはtwitter_accountsテーブルのものを参照するのでこちらでは不要
  protected $visible = [
      'tweet_id_str',
      'tweet_text',
      'tweet_created_at',
      'media_url',
      'owner'
  ];
  
  // fillable
  protected $fillable = [
      'account_id',
      'tweet_id_str',
      'tweet_text',
      'tweet_created_at',
      'media_url',
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
