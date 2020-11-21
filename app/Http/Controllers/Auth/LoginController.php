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
  
    // ================================
    // Twitter連携(Twitter新規登録含む)
    // ================================
    // Twitter認証ページへユーザーをリダイレクトする
    public function redirectToTwitterProvider()
    {
      return Socialite::driver('twitter')->redirect();
    }
    
    // ===================
    // Twitterコールバック
    // ===================
    // Twitterログイン後にでTwitterAPI側から帰ってくる情報たち。
    // Twitterアクセストークンもここで取得する
    public function handleTwitterProviderCallback(){
      
      // twitterアプリ側から返ってきた情報を取得する
      try {
        $twitter_user = Socialite::with("twitter")->user();
        
        Log::debug('twitterアカウント情報を取得しました :' . print_r($twitter_user, true));
  
        // アクセストークンとTwitterIDの取得
        $token = $twitter_user->token;
        $token_secret = $twitter_user->tokenSecret;
        $twitter_id = $twitter_user->id;
        
        // 既にログイン中であれば、設定画面からの連携である
        if(Auth::check()) {
          $auth_user = Auth::user();
          
          // 既に他のユーザーと連携されているTwitterアカウントではないかを確認
          $alreadycheck = User::where('twitter_id', $twitter_user->$twitter_id)->first();
          
          if(isset($alreadycheck)){
            Log::debug('既に他のユーザーが連携しているTwitterアカウントです。');
            return redirect('/mypage')->with('oauth_error', '既に別のユーザーが連携しています。他のアカウントに切り替えて再度お試しください。');
          }
          
          
          // 登録メールアドレスを使ってユーザーレコードを取得する
          $info = User::where('email', $auth_user->email)->first();
          
          // Authしているのでユーザーが取得できないことは無いはずだが、空の場合は処理は行わない。
          if(isset($info)) {
            // 取得したユーザーレコードにtwitter_id,token,token_secretを登録
            $info->fill([
                'twitter_id' => $twitter_id,
                'token' => $token,
                'token_secret' => $token_secret
            ])->save();
            Log::debug('Twitter連携が完了しました。');
          }
          
          return $info;
          
        // ログインしていない場合(新規登録・未ログイン状態からtwitterでログイン・ログイン維持期間のタイムアウト)
        }else{
          // 新規ユーザーによる登録か、連携済みユーザーのログインなのかを判別する。
          // userテーブルのtokenカラムに同一の値を持つレコードがあるかを確認。(emailなどでレコード確認すると、Twitter側のアドレスを変更されたら同一でない判定されてしまうのでtokenを使うこと)
          // レコードがある(連携済みユーザーのログイン時)、$myinfoにそのレコードをオブジェクトで代入
          // レコードがない(新規ユーザーの登録)→第一・第二引数どちらもINSERTしてその情報を$myinfoにオブジェクトで代入する
          $myinfo = User::firstOrCreate(
              [
                  'token' => $token,
                  'token_secret' => $token_secret,
                  'twitter_id' => $twitter_id
              ],
              [
                  'name' => $twitter_user->nickname,
                  'email' => $twitter_user->getEmail(),
                  'token_secret' => $token_secret
              ]);
          Auth::login($myinfo);
  
          // 転送する(トレンド一覧画面が良いか)
          return redirect()->to('/trends');
        }
      }
      catch (\Exception $e) {
        // エラーならログイン画面へ戻す
        Log::debug('ログイン失敗です :'. $e->getMessage());
        return redirect('/login')->with('oauth_error', 'ログインに失敗しました');
      }
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
