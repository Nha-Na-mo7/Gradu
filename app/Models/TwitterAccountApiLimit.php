<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterAccountApiLimit extends Model
{
  /*
   * Twitterアカウント1つに紐づくテーブルです。
   * APIにはフォロー400/1day制限や15/15min制限があり、
   * サービスを利用する上で制限を上回らないよう、フォロー1回ごとにカウントするのがこのテーブルの目的です。
   */
  
  // タイムスタンプ未使用
  public $timestamps = false;
  
  // fillable
  protected $fillable = [
      'account_id',
      'day_follow_count',
      'day_follow_limit_time',
      'fifteen_min_follow_count',
      'fifteen_min_follow_limit_time',
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
