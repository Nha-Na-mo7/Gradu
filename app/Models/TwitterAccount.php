<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterAccount extends Model
{
  // タイムスタンプカラムは用意していないので、無理やり挿入しようとしてエラーにならないようにfalseにする
  public $timestamps = false;
  
  protected $fillable = [
      'account_id',
      'name',
      'screen_name',
      'description',
      'protected',
      'friends_count',
      'followers_count',
      'profile_image_url_https'
  ];
}
