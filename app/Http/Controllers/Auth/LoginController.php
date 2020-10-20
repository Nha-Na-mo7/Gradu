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
    // Twitterログイン後にでTwitterAPI側から帰ってくる情報たち。
    public function handleTwitterProviderCallback(){
      Log::debug('いきスギィ！');
      
      // twitterアプリ側から返ってきた情報を取得する
      try {
        Log::debug('浮きすぎィ！');
  
        // TODO 確認:$user = Socialite::with("twitter")->user();、withメソッドは消滅した？
        // $user = Socialite::driver("twitter")->user();
        $user = Socialite::with("twitter")->user();
      }
      catch (\Exception $e) {
        Log::debug('エラすぎィ！');
        // エラーならログイン画面へ戻す
        return redirect('/login')->with('oauth_error', 'ログインに失敗しました');
      }
      Log::debug('ん...！');
  
      // 新規ユーザーによる登録か、連携済みユーザーのログインなのかを判別する。
      // userテーブルのtokenカラムに同一の値を持つレコードがあるかを確認。(emailなどでレコード確認すると、Twitter側のアドレスを変更されたら同一でない判定されてしまうのでtokenを使うこと)
      // レコードがある(連携済みユーザーのログイン時)、$myinfoにそのレコードをオブジェクトで代入
      // レコードがない(新規ユーザーの登録)→第一・第二引数どちらもINSERTしてその情報を$myinfoにオブジェクトで代入する
      $myinfo = User::firstOrCreate(['token' => $user->token ],
          ['name' => $user->nickname,'email' => $user->getEmail()]);
      Auth::login($myinfo);
  
      /* TODO:  新規ユーザーの登録について
       * ・この例文のコードをそのまま貼り付けてTwitterによる新規登録を行った場合、
       *  パスワードを入力する過程がない為、nullableにしない限りエラーが発生する。
       * ・パスワード未入力で承認させるのは流石にトラブルの元になる為避けたい
       *
       * 【参考:pixiv】
       * ・pixivでは、Twitter新規登録の場合はまずアプリケーション連携を行う。
       * その後、「メールアドレス」「パスワード」「ユーザーネーム」「その他登録に必要な情報」
       * これらを入力させる画面に遷移した。
       * ・おそらく、あらかじめTwitterトークンをhiddenでどこかにinputフォームとして用意しておき、
       * ・フォーム送信の際に同時に入力させる仕様になっている。
       * ・CryptoTrendでもこれを実装したい。
       */
      
      Log::debug('おDBのん');
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
