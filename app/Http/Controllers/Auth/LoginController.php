<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
  
    // ログイン用
    protected function authenticated(Request $request, $user)
    {
      return $user;
    }
    
    
    // // ログイン維持(remember_me)の期間を変更する
    // // Laravelのデフォルトで5年維持するようハードコーディングされてしまっているので
    // //  一度登録されたremember_meトークンを、期間を変更して再登録する。
    // protected function sendLoginResponse(Request $request)
    // {
    //   $request->session()->regenerate();
    //   $this->clearLoginAttempts($request);
    //   $cookies = \Auth::getCookieJar();
    //   $value = $cookies->queued(\Auth::getRecallerName())->getValue();
    //
    //   $cookies->queue(\Auth::getRecallerName(), config('auth.remember_me_expiration'));
    //
    //   return $this->authenticated($request, $this->guard()->user())
    //       ?: redirect()->intended($this->redirectPath());
    // }
    
    // ログアウト用
    // AuthenticatesUsersトレイトの logout メソッド内、loggetOutメソッドのオーバーライドでレスポンスにセッションの再生成を含ませる。
    protected function loggedOut(Request $request)
    {
      // セッションの再生成
      $request->session()->regenerate();
      
      return response()->json();
    }
}
