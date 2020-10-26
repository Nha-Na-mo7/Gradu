<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/*
 * TwitterControllerでのメソッドには大きく2種類
 * ①Twitterアカウント認証がなくても利用できるもの(開発者側のアクセストークンを使うもの)
 * ＞仮想通貨アカウントの取得(表示だけなので、認証は必要ない)
 * ＞通貨毎のツイート数の取得
 * ＞Twitter検索ページへのリンク作成
 *
 * ②認証しなければ使えない機能(認証ユーザーのアクセストークンを利用する)
 * ＞一覧で表示されているアカウントにフォローを飛ばす
 * ＞Twitterアカウントの自動フォロー
 * ＞Twitterログイン
 * ＞(時間があれば)GoogleNewsをツイートする
 *
 * ①に該当するものは.envファイルの設定を反映させ対応する
 * ②に該当するものはDBから各ユーザーのアクセストークンを参照して、各種フォローなどの動作を行う
 *
 * 識別のため各種メソッドの注釈に①または②を付与する。
 *
 */


// TODO 新しい開発者用のユーザーアカウントを新たに作成すること、フォロー0人の開発者用アカウントがないと、鍵垢のユーザーのツイートを晒すことになる
class TwitterController extends Controller
{
    //Twitterのアカウント検索 ①
    public function twitter_index(Request $request)
    {
      // 実行時間。90秒。
      set_time_limit(90);
      
      // 検索キーワード
      $query = $request->keywords;
      
      // API keyなどを定義・エイリアスにするか検討
      $consumer_key = config('services.twitter')['client_id'];
      $consumer_secret = config('services.twitter')['client_secret'];
      $access_token = config('services.twitter')['access_token'];
      $access_token_secret = config('services.twitter')['access_token_secret'];
      
      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
      
      // TwitterAPIにリクエストを投げ、情報を取得する
      // q:必須/検索キーワード
      // page:取得する結果のページを指定します。
      // count:ページごとに取得するユーザー結果の数。（最大値は20）
      // include_entities:entitiesの取得を省略
      $twitterRequest = $connection->get('users/search', array( "q" => $query, "count" => 20));
  
      // TwitterAPIからのレスポンス プロフィール画像のURLから _normalの文字列を省く)
      // _normalを取り除かない場合、48px×48pxのサイズで固定になってしまう
      foreach($twitterRequest as $res){
        $image = $res->profile_image_url_https;
        $fullImg = str_replace('_normal', '', $image);
        $res->full_img = $fullImg;
        $twitterRes[] = $res;
      }
      
      // Vueファイルにデータを返すのでJSON形式
      return response()->json(['result'=>$twitterRequest], 200);
    }

}
