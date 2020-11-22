<?php

namespace App\Models;


use App\Notifications\ChangeEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EmailReset extends Model
{
    // メールの更新時に使うモデル
  
    use Notifiable;
  
    // fillable
    protected $fillable = [
        'user_id',
        'new_email',
        'token'
    ];
    
    // メールアドレス確定メールを送信する
    public function send_email_reset_notification($token)
    {
      $this->notify(new ChangeEmail($token));
    }
    
    // 新しいメールアドレス宛にメールを送信する
    public function routeNotificationForMail($notification)
    {
      return $this->new_email;
    }
}
