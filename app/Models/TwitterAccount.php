<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterAccount extends Model
{
  // タイムスタンプカラムは用意していないので、無理やり挿入しようとしてエラーにならないようにfalseにする
  public $timestamps = false;
  
  // ページネーションで1ページに表示させる数
  protected $perPage = 20;
  
  // JSONに含める属性
  // テーブルidとcreated_atは表示させるのに必要な情報では無いため含めない
  protected $visible = [
      'account_id',
      'name',
      'screen_name',
      'description',
      'protected',
      'friends_count',
      'followers_count',
      'profile_image_url_https',
      'new_tweet',
      'follow'
  ];
  
  // fillable
  protected $fillable = [
      'account_id',
      'name',
      'screen_name',
      'description',
      'protected',
      'friends_count',
      'followers_count',
      'account_created_at',
      'profile_image_url_https'
  ];
  
  /**
   * リレーション - twitter_account_new_tweetsテーブル
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function new_tweet()
  // モデル名と関係ない名前(new_tweet)のため、hasOneメソッドの引数は省略せずに記載
  {
    return $this->hasOne('App\Models\TwitterAccountNewTweet', 'account_id', 'account_id');
  }
  
  /**
   * リレーション - twitter_account_followテーブル
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function follow()
  // モデル名と関係ない名前(new_tweet)のため、hasManyメソッドの引数は省略せずに記載
  {
    return $this->hasMany('App\Models\TwitterAccountFollow', 'account_id', 'account_id');
  }
  
}
