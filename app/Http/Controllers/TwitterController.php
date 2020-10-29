<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\TwitterAccount;
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


// TODO 1、新しい開発者用のユーザーアカウントを新たに作成すること、フォロー0人の開発者用アカウントがないと、鍵垢のユーザーのツイートを晒すことになる
// TODO 2、ログインユーザーがTwitterアカウントを連携している場合、既にフォロー済みのアカウントは表示させない
class TwitterController extends Controller
{

  
    /*
     * バッチ処理の流れ
     * 1, 1日1回、指定時刻になったら、Laravelスケジューラを起動しバッチ処理を開始する
     * ＞指定時刻は朝9時ごろが理想？
     *
     * 2, twitter_indexメソッドを起動し、検索結果を取得する
     * ＞20件ずつしか取得できず、TwitterAPIは1000件までしか対応していない。実際は1019件でるみたいだが...?
     * ＞アカウントが取得できなくなるまでwhile文で回す。
     *
     * 3, 取得した情報は、DBのfetchedAccountsテーブルにそれぞれ必要な情報を格納する
     * ＞テーブル名は仮のもの。
     * ＞既にレコードが存在する場合、一度全てのレコードを空にしてから格納する
     * ＞プライマリーキーもリセットする
     *
     * 4, 仮想通貨アカウント一覧ページにて、fetchedAccountsテーブルから必要情報を取得し表示させる
     *
     * つまり、2 ~ 4までの処理をバッチとしてまとめ、それを1日ごとに実行するのが1での役割である
     * 2 ~ 4をひとまとめにした処理を作成するところからスタートである。
     */
  
  
  
    // 廃止予定メソッド、ページの生合成を保つため、下記にあるtwitter_index2メソッドが完成したら削除
    public function twitter_index(Request $request)
    {
      // 実行時間。90秒。
      set_time_limit(90);
      
      // 検索キーワード
      $query = $request->keywords;
      // ページネーション用、検索ページ
      // TwitterAPIは1ページごとに最大20件までしか取得できないがページネーションに対応している
      $page = $request->page;
      
      // API keyなどを定義・エイリアスにするか検討
      $consumer_key = config('services.twitter')['client_id'];
      $consumer_secret = config('services.twitter')['client_secret'];
      $access_token = config('services.twitter')['access_token'];
      $access_token_secret = config('services.twitter')['access_token_secret'];
      
      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
      
      // TwitterAPIにリクエストを投げ、情報を取得する
      // q:必須/検索キーワード
      // page:取得する結果のページを指定
      // count:ページごとに取得するユーザー結果の数。（最大値は20）
      // include_entities:entitiesの取得を省略(画像など)
      $twitterRequest = $connection->get('users/search', array( "q" => $query, "page" => $page, "count" => 20));
      
      // TwitterAPIからのレスポンス プロフィール画像のURLから _normalの文字列を省く)
      // _normalを取り除かない場合、48px×48pxのサイズで固定になってしまう
      foreach($twitterRequest as $res){
        $image = $res->profile_image_url_https;
        $replaced_fullImg = str_replace('_normal', '', $image);
        $res->replaced_full_img = $replaced_fullImg;
        $twitterRes[] = $res;
      }
      
      // Vueファイルにデータを返すのでJSON形式
      return response()->json(['result'=>$twitterRequest], 200);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // バッチ処理ver Twitterアカウント検索 ①
    // これはバッチ処理で行う。フォローしている、していないの区別をつけることができないようだ。
    public function twitter_index2(Request $request)
    {
      // 実行時間。90秒。
      set_time_limit(90);
      
      $query = '仮想通貨'; // 検索キーワード
      $count = 20; // 1回の取得件数
      $page = 1; // 検索ページ。これを終わるまで繰り返す。
      
      // API keyなどを定義・エイリアスにするか検討
      $consumer_key = config('services.twitter')['client_id'];
      $consumer_secret = config('services.twitter')['client_secret'];
      $access_token = config('services.twitter')['access_token'];
      $access_token_secret = config('services.twitter')['access_token_secret'];
      
      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
  
      // TwitterAPIからのレスポンスの内容を、DBテーブルに登録する
      $twitter_account = new TwitterAccount();
      
      // 0、twitter_accountsテーブルの全レコードを削除し、プライマリーキーをリセット
      $twitter_account->truncate();

      // $pageで検索
      while ($page) {
        // TwitterAPIにリクエストを投げ、情報を取得する
        $twitterRequest = $connection->get('users/search', array("q" => $query, "page" => $page, "count" => $count));
        
        
        // Log::debug('現在ここで詰まっています');
        // $twitter_account->fill($value)->save();にした場合
        // must be of the type array, object given.
        // 配列型である必要があり、オブジェクトが指定されています。
        // つまりオブジェクト型な$valueを配列に戻してあげれば...?
        
        // 取得したアカウントをDBに登録する
        // アカウント検索APIではリツイート・リプライも含めた最新ツイートが取得されてしまう
        // そのためこれより後に改めて該当ユーザーのツイートを取得する
        foreach($twitterRequest as $req => $value){
          // Log::debug('============================');
          // Log::debug('id:' . $value->id);
          Log::debug('name:' . $value->name);
          // Log::debug('screen_name:' . $value->screen_name);
          // Log::debug('description:' . $value->description);
          // Log::debug('protected:' . $value->protected);
          // Log::debug('friends_count:' . $value->friends_count);
          // Log::debug('followers_count:' . $value->followers_count);
          // Log::debug('profile_image_url_https:' . $value->profile_image_url_https);
          
// account_id
// name
// screen_name
// description
// protected
// friends_count
// followers_count
// profile_image_url_https
//           $twitter_account->fill($value)->save();
        }
  
        // 2:取得したアカウントの件数を確認する
        //   件数が最大取得件数(20件)より下回っていた場合、$pageを0にしてループを脱出する
        // if(count($twitterRequest) < $count) {
        //   $page = 0;
        // // 最大取得件数だけ取得していた場合、pageを1増やしてループのはじめに戻る
        // } else {
        //   Log::debug($page);
        //   $page++;
        // }
      }
  
      Log::debug('it is gonenu');
  
      // TwitterAPIからのレスポンス プロフィール画像のURLから _normalの文字列を省く)
      // _normalを取り除かない場合、48px×48pxのサイズで固定になってしまう
      // foreach($twitterRequest as $res){
      //   $image = $res->profile_image_url_https;
      //   $replaced_fullImg = str_replace('_normal', '', $image);
      //   $res->replaced_full_img = $replaced_fullImg;
      //   $twitterRes[] = $res;
      // }
      
      // レスポンスの成功を返すが、バッチ処理なので必要なのかは不明
      return response(200);
    }

}
