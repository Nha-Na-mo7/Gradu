<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\TwitterAccount;
use App\Models\TwitterAccountNewTweet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function Psy\debug;

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
     * バッチ処理の流れ1 - Twitterユーザー編 -
     * 1, 1日1回、指定時刻になったら、Laravelスケジューラを起動しバッチ処理を開始する
     * ＞指定時刻は朝9時ごろが理想？
     *
     * 2, twitter_indexメソッドを起動し、検索結果を取得する
     * 3, 取得した情報は、twitter_accountsテーブルに格納する
     *
     * 4, twitter_accountsテーブルに登録した情報を元にして、最新のツイートを取得する
     * ＞ users/searchAPIでは、リツイートやリプライも無作為に取得してしまうため
     * ＞ また、鍵アカウントの区別が無いため不要なエラーの原因にもなる
     * ＞ 鍵アカウントであった場合、その項目をnull扱いにすることで対処できる
     *
     * 5, 仮想通貨アカウント一覧ページにて、fetchedAccountsテーブルから必要情報を取得し表示させる
     *
     * つまり、2 ~ 4までの処理をバッチとしてまとめ、それを1日ごとに実行するのが1での役割である
     */
  
  
    /*
     * バッチ処理の流れ2 - 仮想通貨人気ツイート編 -
     * 1, 指定時刻になったら、Laravelスケジューラを起動しバッチ処理を開始する。
     * → 過去1時間、過去1日、過去1週間でランキング表示の条件を指定できるようにする
     * このサービスの目的は「トレンド」を知ること
     * 24時間区切りの「1時間前」は当てにならないので...
     *
     * 1H : 1時間ごとに取得(毎時:30分)
     * 24H : 1時間ごとに取得(毎時:30分)
     * 7Days : 1日ごとに取得(毎日:00:00)
     *
     * それぞれ3テーブル分用意する
     *
     *
     *
     *
     *
     * ETCをどのように除外するか
     *
     * TODO メソッド名は仮のものです
     * 2, twitter_trendメソッドを起動し、検索結果を取得する
     * TODO テーブル名も仮のものです
     * 3, 取得した情報は、twitter_brand_tweetsテーブルに格納する
     *
     * 4, twitter_accountsテーブルに登録した情報を元にして、最新のツイートを取得する
     * ＞ users/searchAPIでは、リツイートやリプライも無作為に取得してしまうため
     * ＞ また、鍵アカウントの区別が無いため不要なエラーの原因にもなる
     * ＞ 鍵アカウントであった場合、その項目をnull扱いにすることで対処できる
     *
     * 5, 仮想通貨アカウント一覧ページにて、fetchedAccountsテーブルから必要情報を取得し表示させる
     *
     * つまり、2 ~ 4までの処理をバッチとしてまとめ、それを1日ごとに実行するのが1での役割である
     */
  
    // 廃止予定メソッド、ページの生合成を保つため、下記にあるtwitter_index2メソッドが完成したら削除
    public function twitter_index_old(Request $request)
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
    
    // ==================================
    // バッチ処理ver Twitterアカウント検索 ①
    // ==================================
    // これはバッチ処理で行う。フォローしている、していないの区別をつけることができないようだ。
    public function twitter_index()
    {
      $query = '仮想通貨'; // 検索キーワード
      $count = 20; // 1回の取得件数
      $page = 50; // 検索ページ。これを終わるまで繰り返す。
      
      // API keyなどを定義・エイリアスにするか検討
      $consumer_key = config('services.twitter')['client_id'];
      $consumer_secret = config('services.twitter')['client_secret'];
      $access_token = config('services.twitter')['access_token'];
      $access_token_secret = config('services.twitter')['access_token_secret'];
      
      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
      
      // twitter_accountsテーブルの全レコードを削除
      TwitterAccount::query()->delete();

      // $pageで検索
      while ($page) {
        Log::debug($page.'ページ目をチェックします');
        // TwitterAPIにリクエストを投げ、情報を取得する
        $twitterRequest = $connection->get('users/search', array("q" => $query, "page" => $page, "count" => $count));
        
        // 取得件数が0(配列になっていない)ならループを抜ける
        if(!is_array($twitterRequest)) {
          break;
        }
        
        // 取得したアカウント情報をDBに登録する
        // アカウント検索API(users/search)ではリツイート・リプライも含めた最新ツイートが取得されてしまうため、後に改めて該当ユーザーの最新ツイートを取得する
        foreach($twitterRequest as $req){
          
          $account_id = $req->id;
          
          // プロフィール画像のURLから _normalの文字列を省く
          // _normalを取り除かない場合、48px×48pxのサイズで固定になってしまう
          $image = $req->profile_image_url_https;
          $replaced_fullImg = str_replace('_normal', '', $image);

          // 配列に格納
          $requestlist = array(
              'account_id' => $account_id,
              'name' => $req->name,
              'screen_name' => $req->screen_name,
              'description' => $req->description,
              'protected' => $req->protected,
              'friends_count' => $req->friends_count,
              'followers_count' => $req->followers_count,
              'account_created_at' => date('Y-m-d H:i:s', strtotime($req->created_at)),
              'profile_image_url_https' => $replaced_fullImg
          );
  
          // TwitterAccountモデルを作成
          $twitter_account = new TwitterAccount();
          // テーブル登録
          $twitter_account->fill($requestlist)->save();
          
          // accountsテーブルに登録後、
          // TwitterAPIに投げて、リプライ・リツイートでは無い最新のツイート1件を探す
          // GET statuses/user_timelineを使う
          // user_idまたはscreen_idが検索には必須だが、screen_idは変更される場合があるのでuser_idで検索をかける
          
          // ツイートをしていない場合→ []で帰ってくる
          // 鍵垢→ Not authorized
          
          // リプライを含めない exclude_replies→true
          // RTを含め無い include_rts→false
          // ややこしい
          
          // 鍵垢では無い場合、最新ツイート検索をする
          if(!$req->protected) {
  
            $tweetRequest = $connection->get('statuses/user_timeline',
                array(
                    "user_id" => $account_id,
                    "count" => 1,
                    "exclude_replies" => true,
                    "include_rts" => false
                ));
            
            // 取得したツイートの内容から、表示に必要な情報を抽出して配列に格納
            foreach ($tweetRequest as $tweetreq) {
              // ツイートが一つも無い場合、空配列で帰ってくるため中身があるかを確認
              
              // TODO page52に差し掛かった段階で、id_strを参照しようとしてエラーを履いてしまう。要検証。
              // TODO タイムアウトの変更ができていないためエラーが吐かれてしまう。
              
              if(isset($tweetreq)) {
                $addlist = array(
                    'account_id' => $account_id,
                    'tweet_id_str' => $tweetreq->id_str,
                    'tweet_text' => $tweetreq->text,
                    'tweet_created_at' => date('Y-m-d H:i:s', strtotime($tweetreq->created_at))
                );
                $tweetlist = $addlist;
              // まだツイートしていないアカウントは、アカウントのIDだけテーブルに入れる
              } else {
                $tweetlist = array('account_id' => $account_id);
              }
            }
          // 鍵垢の場合は、アカウントのIDだけをテーブルにいれる
          } else {
            $tweetlist = array('account_id' => $account_id);
          }
          // モデルを作成
          $new_tweet = new TwitterAccountNewTweet();
          // テーブル登録
          $new_tweet->fill($tweetlist)->save();

        }
        // ページカウントを1増やす
        $page++;
      }
      // TODO レスポンスの成功を返すが、バッチ処理なので必要なのかは不明
      return response(200);
    }
  
    
    // =======================================
    // アカウント一覧ページ/DBからアカウント一覧の取得
    // =======================================
    public function accounts_index()
    {
      $accounts = TwitterAccount::with(['new_tweet'])->orderBy('account_created_at', 'desc')->paginate();
      
      return $accounts;
    }
    
    // =======================================
    // 指定したアカウントをフォローする
    // =======================================
    public function accounts_follow(Request $request)
    {
      /* POST friendships/create - フォローする
       *
       * screen_nameでもフォローが出来るが、ユーザーに変更される可能性があるため不変のuser_idを指定する。
       * user_id:必須 / フォロー先のアカウントID
       * follow: false (ちなみにtrueにするとお気に入り通知もONにしてくれるらしい)
       */
      $user_id = $request->user_id; // フォロー対象のアカウントのID。
      
      // API keyなどを定義・エイリアスにするか検討
      $consumer_key = config('services.twitter')['client_id'];
      $consumer_secret = config('services.twitter')['client_secret'];
      $access_token = config('services.twitter')['access_token'];
      $access_token_secret = config('services.twitter')['access_token_secret'];
      
      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
      $twitterRequest = $connection->post('friendships/create', array("user_id" => $user_id));
      
      return response(200);
    }
    // // =========================================
    // // アカウントコンポーネント/DBから新着ツイートの取得
    // // =========================================
    // public function accounts_tweet(int $tweet_id)
    // {
    //   Log::debug('TwitterController : accounts_tweet : アカウントのツイートを取得');
    //   $tweet = TwitterAccountNewTweet::where('account_id', $tweet_id)->first();
    //
    //   return $tweet;
    // }

}
