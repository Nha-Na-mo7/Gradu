<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class TwitterController extends Controller
{
    // Twitterのインスタンス作成などのコントローラです。
    // 自動フォローや仮想通貨アカウント検索はTwitterAccountListControllerを
    // トレンド通貨集計はTrendTweetControllerを参照してください。

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
  
  
    
    // ========================================
    // 設定画面からTwitterとサービスを連携させる
    // ========================================
    public function linkage() {
    
    }
    
    // ========================================
    // Twitterとの連携を解除する
    // ========================================
    public function un_linkage(){
        Log::debug('TwitterController.un_linkage 連携解除');
        try {
          // usersテーブルのアクセストークンを見てtwitterログインやオートフォローなどの処理を行っている
          // →アクセストークンの項目を空にすれば連携は解除される
          $user = (new UserController)->auth_user();
          
          // usersテーブルのTwitterID・アクセストークン・シークレットを削除する。(fillでnullが入れられないのでベタがき)
          $user->twitter_id = null;
          $user->token = null;
          $user->token_secret = null;
          $user->save();
          
          Log::debug('Twitter連携を解除しました。');
      
          return response()->json(['success' => 'Twitter連携を解除しました。'], 200);
        } catch (\Exception $e) {
          Log::debug('アカウント情報変更時に例外が発生しました。' .$e->getMessage());
          return response()->json(['errors' => '連携解除時にエラーが発生しました。しばらくしてからもう一度お試しください。'], 500);
        }
    }
  
  
    // ================================
    // Twitter連携(Twitter新規登録含む)
    // ================================
    // Twitter認証ページへユーザーをリダイレクトする
    public function redirectToTwitterProvider()
    {
      Log::debug('=========================================================');
      Log::debug('TwitterController.redirectToTwitterProcider Twitter認証開始');
      Log::debug('=========================================================');
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
        
        Log::debug('twitterアカウント情報を取得しました :' . print_r($twitter_user, true));
        
        // アクセストークンとTwitterIDの取得
        $token = $twitter_user->token;
        $token_secret = $twitter_user->tokenSecret;
        $twitter_id = $twitter_user->id;
        
        Log::debug('これからログイン済みかのチェックです');
        // 既にログイン中であれば、設定画面からの連携である
        if(Auth::check()) {
          Log::debug('--- ログイン済みです。');
          $auth_user = Auth::user();
          
          // 既に他のユーザーと連携されているTwitterアカウントの場合、returnさせる
          Log::debug('既に他のユーザーと連携されているTwitterアカウントではないかをチェックします');
          $alreadycheck = User::where('twitter_id', $twitter_user->id)->first();
          
          if(!empty($alreadycheck)){
            Log::debug('> 他のユーザーが連携しているTwitterアカウントです。連携処理は行わずにレスポンスして終了します。');
            Log::debug('===================================================================');
            // TODO フラッシュ
            return redirect('/mypage')->with('oauth_error', '既に別のユーザーが連携しています。他のアカウントに切り替えて再度お試しください。');
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
          }
  
          // TODO ここでフォローしている人を取得しDBに格納するメソッドを発動
          Log::debug('アカウント設定画面にリダイレクトします');
          return redirect()->to('/mypage');
          
        // ログインしていない場合(新規登録・未ログイン状態からtwitterでログイン・ログイン維持期間のタイムアウト)
        }else{
          Log::debug('ログインしていません。ログインor新規登録です。');
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
          
          // TODO ここでフォローしている人を取得しDBに格納するメソッドを発動
          
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
}
