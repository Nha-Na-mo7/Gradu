<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    
    public function __construct()
    {
      $this->middleware('guest');
    }
  
  
    // ===================================
    // バリデータ
    // ===================================
    protected function validator(array $data)
    {
      return Validator::make($data, [
          'email' => ['required', 'string', 'email:strict,dns,spoof', 'max:100'],
      ]);
    }
    
    // ===================================
    // メール送信 トレイトオーバーライド
    // ===================================
    public function sendResetLinkEmail(Request $request)
    {
      $this->validateEmail($request);
    
      $response = $this
          ->broker()
          ->sendResetLink($request->only('email'));
  
      return $response == Password::RESET_LINK_SENT
          ? $this->sendResetLinkResponse($request, $response)
          : $this->sendResetLinkFailedResponse($request, $response);
      
    }
  
    // ===================================
    // 偽装メール送信通知 (トレイトオーバーライド)
    // ===================================
   /**
     * SendsPasswordResetEmails trait で定義されているメソッドをオーバーライド。
     * usersテーブルに登録されていないメアドが入力されていた場合でも、「送信しました」とメッセージを出す。
     * ただし実際にはメールを送らない。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
      sleep(2);
      // レスポンスを成功したときと同様の処理に書き換える。
      $response = 'passwords.sent';
      // 成功時と同じようにバリデーションエラーは出さないで戻す
      return back()->with('status', trans($response));
    }
}
