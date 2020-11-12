<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowTarget extends Model
{
  // 1つのtwitterアカウントがフォローしているアカウントIDのテーブルです
  // usersテーブルに紐づきます(1:多)
  
  public $timestamps = false;
  
  protected $fillable = [
      'account_id',
      'follow_target_id'
  ];
  
  /**
   * リレーション - usersテーブル
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function owner()
  {
    return $this->belongsTo('App\Models\User', 'account_id', 'twitter_id', 'users');
  }
}
