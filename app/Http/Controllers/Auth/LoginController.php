<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

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
  
    // =========================================
    // フォームからのログイン(トレイトをオーバーライド)
    // =========================================
    protected function authenticated(Request $request, $user)
    {
      // ログイン画面に移行する前にtwitter_idをセッションに登録する
      $twitter_id = $user->twitter_id;
      
      if(!empty($twitter_id)) {
        session()->put('twitter_id', $twitter_id);
      }
      
      // ログイン処理後にリダイレクトする先を指定する
      // ここで$userなんてしたら認証されたユーザー情報をprintするだけの画面になるのでやらないように。
      return redirect()->to('/mypage');
    }
    
    // ===================================
    // バリデーション (トレイトのオーバーライド)
    // ===================================
    protected function validateLogin(Request $request)
    {
      $request->validate([
          $this->username() => 'required|email:strict,dns,spoof|max:100',
          'password' => 'required|string|min:8|max:50|regex:/^[a-zA-Z0-9]+$/',
      ]);
    }
    
    // ===================================
    // ログアウト
    // ===================================
    // 本来はログアウト後のリダイレクト先を設定するトレイト。
    // AuthenticatesUsersトレイトの logout メソッド内、
    // loggetOutメソッドのオーバーライドでレスポンスにセッションの再生成を含ませる。
    protected function loggedOut(Request $request)
    {
      // セッションの再生成
      $request->session()->regenerate();

      return redirect('/');
    }
}
