<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

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
  
    public function sendResetLinkEmail(Request $request)
    {
      $this->validateEmail($request);
    
      $response = $this->broker()->sendResetLink(
          $request->only('email')
      );
      
      if($response == Password::RESET_LINK_SENT) {
        Log::debug('this is true(201)');
        return response()->json([
            'message' => 'Reset link sent to your email.',
            'status' => true
        ], 201);
      }else{
        Log::debug('this is false(401)');
  
        return response()->json([
            'message' => 'Unable to sent reset link', 'status' => false
        ], 401);
      }
    }
  
  
  /**
     * SendsPasswordResetEmails trait で定義されているメソッドをオーバーライド。
     * usersテーブルに登録されていないメアドが入力されていた場合でも、「送信しました」とメッセージを出す。
     * ただし実際にはメールを送らない。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
      // もともとはこちらの処理
      //return back()
      //        ->withInput($request->only('email'))
      //        ->withErrors(['email' => trans($response)]);
      // レスポンスを成功したときと同様の処理に書き換える。
      $response = 'passwords.sent';
      // 成功時と同じようにバリデーションエラーは出さないで戻す
      return back()->with('status', trans($response));
    }
}
