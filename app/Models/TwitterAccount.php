<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterAccount extends Model
{
  protected $fillable = [
      'account_id',
      'name',
      'screen_name',
      'description',
      'protected',
      'friends_count',
      'followers_count',
      'status_id_str',
      'status_text',
      'status_created_at',
      'profile_image_url_https'
  ];
}
