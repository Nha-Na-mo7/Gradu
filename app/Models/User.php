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
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
}
