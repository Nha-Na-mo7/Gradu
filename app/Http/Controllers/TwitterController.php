<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;

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
}
