<?php

namespace App\Models;

use App\Notifications\CustomPasswordReset;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'twitter_id', 'token', 'token_secret', 'test_user_flg'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * パスワードリセット通知の送信
     * CanResetPasswordトレイトで定義されているsendPasswordResetNotificationをオーバーライド
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
      $this->notify(new CustomPasswordReset($token));
    }
  
  
    /**
     * リレーション - follow_api_limitsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function api_limit()
      // モデル名と関係ない名前(api_limit)のため、hasOneメソッドの引数は省略せずに記載
    {
      return $this->hasOne('App\Models\FollowApiLimit', 'twitter_id', 'account_id');
    }
  
}
