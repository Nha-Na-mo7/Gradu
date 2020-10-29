<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterAccount extends Model
{
  // タイムスタンプカラムは用意していないので、無理やり挿入しようとしてエラーにならないようにfalseにする
  public $timestamps = false;
  
  // ページネーションで1ページに表示させる数
  protected $perPage = 20;
  
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
}
