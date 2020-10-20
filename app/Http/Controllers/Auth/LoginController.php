<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
  
    // ====================
    // ログイン(フォームから)
    // ====================
    protected function authenticated(Request $request, $user)
    {
      return $user;
    }
  
    // ====================
    // ログイン(Twitter)
    // ====================
    // Twitter認証ページへユーザーをリダイレクトする
    public function redirectToTwitterProvider()
    {
      return Socialite::driver('twitter')->redirect();
    }
    
    // ===================
    // Twitterコールバック
    // ===================
    public function handleTwitterProviderCallback(){
      
      // twitterアプリ側から返ってきた情報を取得する
      try {
        // TODO 確認:$user = Socialite::with("twitter")->user();、withメソッドは消滅した？
        // $user = Socialite::driver("twitter")->user();
        $user = Socialite::with("twitter")->user();
      }
      catch (\Exception $e) {
        // エラーならログイン画面へ戻す
        return redirect('/login')->with('oauth_error', 'ログインに失敗しました');
      }
      
      // userテーブルのtokenカラムに同一の値を持つレコードがあるかを確認
      // レコードがある時、$myinfoにそのレコードをオブジェクトで代入
      // レコードがない場合→第一・第二引数どちらもINSERTしてその情報を$myinfoにオブジェクトで代入する
      
      $myinfo = User::firstOrCreate(['token' => $user->token ],
          ['name' => $user->nickname,'email' => $user->getEmail()]);
      Auth::login($myinfo);
      return redirect()->to('/'); // ホームへ転送
      
    }
    
    // // =======================================
    // // ログイン維持(remember_me)の期間を変更する
    // // =======================================
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
  
    // =============
    // ログアウト
    // =============
    // AuthenticatesUsersトレイトの logout メソッド内、loggetOutメソッドのオーバーライドでレスポンスにセッションの再生成を含ませる。
    protected function loggedOut(Request $request)
    {
      // セッションの再生成
      $request->session()->regenerate();
      
      return response()->json();
    }
}
