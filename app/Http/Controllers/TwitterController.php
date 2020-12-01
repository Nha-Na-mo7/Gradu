<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class TwitterController extends Controller
{
    // Twitterのインスタンス作成、連携処理などのコントローラです。
    // 自動フォローや仮想通貨アカウント検索はTwitterAccountListControllerを
    // トレンド通貨集計はTrendTweetControllerを参照してください。
  
    // Twitter連携関係でフラッシュメッセージに使う
    const ALREADY_EXIST_ACCOUNT_ERROR_MSG = 'Twitterに登録されたメールアドレスを用いたCryptoTrendアカウントが既に存在しています。別のTwitterアカウントを使うか、一度該当メールアドレスでご登録後に連携処理を行ってください。';
    const OTHER_USED_ACCOUNT_ERROR_MSG =  '既に別のユーザーが連携しています。他のアカウントに切り替えて再度お試しください。';
    const FAIL_LOGIN_MSG = 'Twitterログインに失敗しました。';
    const SUCCESS_LINKAGE = 'Twitterアカウントと連携しました！';
    const SUCCESS_TWITTER_LOGIN = 'Twitterアカウントでログインしました！';

    
    // =======================================
    // 認証ユーザーによるコネクションインスタンスの作成
    // =======================================
    // 引数は、ユーザーのアクセストークン と アクセストークンシークレットの2つ
    public function connection_instanse_users($token, $token_secret)
    {
      $consumer_key = config('services.twitter')['client_id'];
      $consumer_secret = config('services.twitter')['client_secret'];
      // ユーザーのアクセストークン
      $access_token = $token;
      $access_token_secret = $token_secret;
      
      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
      
      return $connection;
    }
    
    // =======================================
    // アプリケーションによるコネクションインスタンスの作成
    // =======================================
    public function connection_instanse_app()
    {
      $consumer_key = config('services.twitter')['client_id'];
      $consumer_secret = config('services.twitter')['client_secret'];
      $access_token = config('services.twitter')['access_token'];
      $access_token_secret = config('services.twitter')['access_token_secret'];
      
      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
      
      return $connection;
    }
    
    // ========================================================
    // ベアラートークンを取得してアプリケーション認証用のインスタンスを作成
    // ========================================================
    public function connection_instanse_OAuth2()
    {
      $connection = $this->connection_instanse_app();
      
      // アプリ認証用のベラトークンを取得
      $_bearer_token = $connection->oauth2("oauth2/token", array("grant_type" => "client_credentials"));
  
      // ベラトークンをセット
      if(isset($_bearer_token->access_token)){
        $connection->setBearer($_bearer_token->access_token);
      }
      
      return $connection;
    }
    
    // ================================
    // Twitter連携の開始
    // ================================
    // Twitter認証ページへユーザーをリダイレクトする
    public function redirectToTwitterProvider()
    {
      Log::debug('=========================================================');
      Log::debug('TwitterController.redirectToTwitterProcider Twitter認証開始');
      Log::debug('=========================================================');
      // アクセス元の_urlをセッションに格納
      \Session::flash('url',\Request::server('HTTP_REFERER'));
  
      return Socialite::driver('twitter')->redirect();
    }
  
    
    // ================================
    // Twitterコールバック
    // ================================
    // Twitterログイン後にでTwitterAPI側から帰ってくる情報たち。
    // Twitterアクセストークンもここで取得する
    public function handleTwitterProviderCallback(){
      Log::debug('==========================================================');
      Log::debug('TwitterController.handleTwitterProciderCallback コールバック');
      Log::debug('==========================================================');
      
      // twitterアプリ側から返ってきた情報を取得する
      try {
        $twitter_user = Socialite::with("twitter")->user();
        
        Log::debug('twitterアカウント情報を取得しました。');
        // Log::debug(print_r($twitter_user, true));
        
        // アクセストークンとTwitterIDの取得
        $token = $twitter_user->token;
        $token_secret = $twitter_user->tokenSecret;
        $twitter_id = $twitter_user->id;
        
        Log::debug('ログイン済みかチェックします');
        // 既にログイン中であれば、設定画面からの連携である
        if(Auth::check()) {
          Log::debug('--- ログイン済み ( = 設定画面からの連携処理 )です。');
          $auth_user = Auth::user();
          
          // 既に他のユーザーと連携されているTwitterアカウントの場合、returnさせる
          $alreadycheck = User::where('twitter_id', $twitter_user->id)->first();
          
          if(!empty($alreadycheck)){
            Log::debug('他のユーザーが連携しているTwitterアカウントです。連携処理は行わずにレスポンスして終了します。');
            Log::debug('===================================================================');

            return redirect(\Session::get('url'))->with('system_message', self::OTHER_USED_ACCOUNT_ERROR_MSG);
          }
          
          // 登録メールアドレスを使ってユーザーレコードを取得する
          Log::debug('他に連携されているユーザーは存在しませんでした。ユーザーレコードを取得します');
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
  
            // フォローしている人を取得し、followsテーブルに格納する
            Log::debug('ただいま連携したユーザーがフォローしているユーザーをfollowsテーブルに格納します。');
            $TwitterAccountList = new TwitterAccountListController();
            $account_list = $TwitterAccountList->make_array_account_list_ids();
            $TwitterAccountList->insert_db_to_follows_by_following($info, $account_list);
            Log::debug('フォロー中 かつ accountsテーブルに保存されているユーザーをfollowsテーブルに格納しました。');
          }
  
          Log::debug('リダイレクトします。');
          return redirect(\Session::get('url'))->with('system_message', self::SUCCESS_LINKAGE);
          
        // ログインしていない場合(新規登録・未ログイン状態からtwitterでログイン)
        }else{
          Log::debug('ログインしていません。ログインor新規登録です。');
          // 新規ユーザーによる登録か、連携済みユーザーのログインなのかを判別する。
          // userテーブルのtokenカラムに同一の値を持つレコードがあるかを確認。(emailなどでレコード確認すると、Twitter側のアドレスを変更されたら同一でない判定されてしまうのでtokenを使うこと)
          $myinfo = User::where('twitter_id', $twitter_id)
              ->first();
  
          // 連携されているアカウントレコードがない場合(新規ユーザーの登録)
          if(empty($myinfo)) {
            // Twitterに登録しているメアドが既に他のユーザーと連携されていないかを確認
            Log::debug('Twitterに登録しているメールアドレスを使ったCryptoアカウントが既に存在する場合はreturnします。');
            $already_email_registered = User::where('email', $twitter_user->email)->first();
            
            // 既に登録済みのメアドを用いたCryptoアカウントがあるなら、登録をせずそのまま元の画面に戻す。
            if(!empty($already_email_registered)){
              Log::debug($twitter_user->email.'を使ったCryptoアカウントは既に存在しています。連携・ログイン共に行わず終了します。');
              Log::debug('===================================================================');
              
              // 元の画面に戻し、フラッシュメッセージで連携失敗を説明する
              return redirect(\Session::get('url'))
                  ->with('system_message', self::ALREADY_EXIST_ACCOUNT_ERROR_MSG);
            }
            
            // 新規登録の処理
            $myinfo = User::create([
                'token' => $token,
                'token_secret' => $token_secret,
                'twitter_id' => $twitter_id,
                'name' => $twitter_user->nickname,
                'email' => $twitter_user->getEmail()
            ]);
            Log::debug('新規登録処理を行いました。');
          }
          
          // ログイン時 or 新規登録時にも、フォローしている人を取得し、followsテーブルに格納する
          Log::debug('ユーザーがフォローしているユーザーをfollowsテーブルに格納します');
          $TwitterAccountList = new TwitterAccountListController();
          $account_list = $TwitterAccountList->make_array_account_list_ids();
          $TwitterAccountList->insert_db_to_follows_by_following($myinfo, $account_list);
          Log::debug('フォロー中 かつ accountsテーブルに保存されているユーザーをfollowsテーブルに格納しました。');
          
          // Twitterログインの場合は利便性重視のユーザーが想定されるので、継続ログインをONとする
          Auth::login($myinfo, true);
          
          // トレンド一覧ページへ転送する
          return redirect('trends')->with('system_message', self::SUCCESS_TWITTER_LOGIN);
        }
      }
      catch (\Exception $e) {
        // エラーならログイン画面へ戻す
        Log::debug('ログイン失敗しました。 :'. $e->getMessage());
        
        // セッションを一度消してから再発行、csrfトークンを再生成
        session()->invalidate();
        session()->regenerateToken();
        
        // ログイン画面へ戻す
        return redirect()->intended('/login')->with('system_message', self::FAIL_LOGIN_MSG);
      }
    }
    
    // ========================================
    // Twitterとの連携を解除する
    // ========================================
    public function un_linkage(){
      Log::debug('TwitterController.un_linkage 連携解除');
      try {
        // usersテーブルのアクセストークンを見てtwitterログインやオートフォローなどの処理を行っている
        // →アクセストークンの項目を空にすれば連携は解除される
        $user = Auth::user();
        
        // usersテーブルのTwitterID・アクセストークン・シークレットを削除する。オートフォローはオフにする。
        $user->twitter_id = null;
        $user->token = null;
        $user->token_secret = null;
        $user->auto_follow_flg = false;
        $user->save();
        
        Log::debug('Twitter連携を解除しました。');
        
        return response()->json(['success' => 'Twitterの連携を解除しました。'], 200);
      } catch (\Exception $e) {
        Log::debug('アカウント情報変更時に例外が発生しました。' .$e->getMessage());
        return response()->json(['errors' => '連携解除時にエラーが発生しました。しばらくしてからもう一度お試しください。'], 500);
      }
    }
}
