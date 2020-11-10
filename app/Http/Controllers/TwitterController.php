<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\TwitterAccount;
use App\Models\TwitterAccountFollow;
use App\Models\TwitterAccountNewTweet;
use App\Models\UpdatedAtTable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;
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
    const DAILY_LIMIT_FOLLOW = 400; // twitterAPIが1日にフォローできる最大数
    const MIN_LIMIT_APP_FOLLOW = 15; // 15/15minを超えると制限にかかる
  
    const UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID = 1; // updated_at_tablesにおけるtwitter_accountsを参照するテーブルのID
  
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
    
    // ==================================
    // バッチ処理ver Twitterアカウント検索 ①
    // ==================================
    // これはバッチ処理で行う。フォローしている、していないの区別をつけることができないようだ。
    public function search_accounts()
    {
      Log::debug('==============================================');
      Log::debug('TwitterController.search_accounts アカウント検索');
      Log::debug('==============================================');
      
      // --------------------------
      // APIリクエストの前準備
      // --------------------------
  
      $query = '仮想通貨'; // 検索キーワード
      $count = 20; // 1回の取得件数
      $page = 51; // 検索ページ。これを終わるまで繰り返す。
      
      // ループ内で一度更新処理を行ったアカウントのIDを格納する
      $updateded_accounts = [];
      
      // API使用のためのインスタンスの作成
      $connection = $this->connection_instanse_app();
  
      try {
        // $pageで検索する。APIの仕様上51ページまで取得できる。
        while ($page < 52) {
          Log::debug('▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼ ' . $page . ' ページ目をチェックします ▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼');
    
          // -------------------------
          // アカウント検索APIにリクエスト
          // -------------------------
          // 仮想通貨ユーザーを取得する
          $result_users = $connection->get('users/search', array(
              "q" => $query,
              "page" => $page,
              "count" => $count
          ));
    
          // ------------------------------
          // 取得したアカウント情報をDBに登録する
          // ------------------------------
          // アカウント検索APIではRT・リプライも含めた最新ツイートが取得されてしまうため、改めて該当ユーザーの最新ツイートを取得する
          // このAPIは重複した結果を返すこともあるため、同じユーザーが出現した場合登録せずツイート検索も行わない
          foreach ($result_users as $user) {
      
            $request = json_decode(json_encode($user), true);
            // Log::debug('解体: '. print_r($request, true));
      
            // エラーコードが存在する場合
            if (isset($request[0]['code'])) {
              Log::debug('エラー code:' . $request[0]['code']);
              break 2;
            }
      
            // idは複数回使うので変数に格納
            $account_id = $request['id'];
      
            // --------------------------
            // 取得したユーザーの重複チェック
            // --------------------------
            // 取得したIDが既にテーブルにあるか探す
            $result_by_db = TwitterAccount::where('account_id', $account_id)->get();
      
            // 新規登録かを判定するフラグ(falseであればアップデートになる)
            $insert_flg = false;
      
            // 重複がない(IDがない)場合
            if ($result_by_db->isEmpty()) {
              $insert_flg = true;
              Log::debug('テーブルに存在しないアカウントでした。新規登録に入ります。');
              // 既にテーブル登録済みの場合
            } else {
              // 既に登録済みのアカウントは、update処理を行う。
              Log::debug('このアカウント(ID:' . $account_id . ')は既にDBに登録済みでした。更新処理に入ります。');
              // 一度ループ内でアップデート処理を行っているアカウントかを確認する
              // 配列内に、今回のIDが存在するかを確認
              $isUpdated_id = in_array($account_id, $updateded_accounts);
        
              if ($isUpdated_id) {
                Log::debug('ID:' . $account_id . ' は既にアップデート済みなのでcontinueします。');
                continue;
              }
            }
      
            // プロフィール画像のURLから _normalの文字列を省く
            // _normalを取り除かない場合、48px×48pxのサイズで固定になってしまう
            $image = $request['profile_image_url_https'];
            $replaced_fullImg = str_replace('_normal', '', $image);
      
            // 配列に格納
            $requestlist = array(
                'account_id' => $account_id,
                'name' => $request['name'],
                'screen_name' => $request['screen_name'],
                'description' => $request['description'],
                'protected' => $request['protected'],
                'friends_count' => $request['friends_count'],
                'followers_count' => $request['followers_count'],
                'account_created_at' => date('Y-m-d H:i:s', strtotime($request['created_at'])),
                'profile_image_url_https' => $replaced_fullImg
            );
      
            // --------------------------
            // 最新のツイート1件検索
            // --------------------------
            // GET statuses/user_timelineを使う
            // screen_idは変更される場合があるので、user_idで検索をかける
      
            // ツイートをしていない場合: []で帰ってくる
            // 鍵垢: Not authorized
      
            // 鍵垢では無い場合に最新ツイート検索をする
            if (!$request['protected']) {
        
              Log::debug('公開アカウントでしたので、ツイート検索APIにリクエストします。');
              // APIにリクエストする
              $tweetRequest = $connection->get('statuses/user_timeline',
                  array(
                      "user_id" => $account_id,
                      "count" => 1,
                      "exclude_replies" => true,
                      "include_rts" => false
                  ));
        
              // 取得したツイートの内容から、表示に必要な情報を抽出して配列に格納
              foreach ($tweetRequest as $tweetreq) {
                $addlist = array(
                    'account_id' => $account_id,
                    'tweet_id_str' => $tweetreq->id_str ?? null,
                    'tweet_text' => $tweetreq->text ?? null,
                );
                $created_at = $tweetreq->created_at ?? null;
                if ($created_at !== null) {
                  $addlist['tweet_created_at'] = date('Y-m-d H:i:s', strtotime($created_at));
                }
                $tweetlist = $addlist;
              }
              // 鍵垢の場合は、アカウントのID以外nullとしてテーブルにいれる
              // これは更新処理の時、公開→鍵垢へ変わっていたユーザーのツイートをnullとして更新するため
            } else {
              Log::debug('ID:' . $account_id . ' は非公開アカウントでした。アカウントID以外はnullとして登録します。');
              $tweetlist = array(
                  'account_id' => $account_id,
                  'tweet_id_str' => null,
                  'tweet_text' => null,
                  'tweet_created_at' => null,
              );
            }
      
            // ----------------------
            // DB登録or更新
            // ----------------------
            // 新規登録
            if ($insert_flg) {
              Log::debug('これよりデータベースへ新規登録を行います。');
              // TwitterAccountモデルを作成
              $twitter_account = new TwitterAccount();
              // TwitterAccountNewTweetモデルを作成
              $new_tweet = new TwitterAccountNewTweet();
        
              // テーブルに登録
              try {
                // twitter_accountsテーブルへの登録
                $twitter_account->fill($requestlist)->save();
                Log::debug('twitter_accountsテーブルに新規登録しました。');
          
                // twitter_account_new_tweetsテーブルへの登録
                $new_tweet->fill($tweetlist)->save();
                Log::debug('twitter_account_new_tweetsテーブルに新規登録しました。');
          
              } catch (\Exception $exception) {
                Log::debug('DBへの新規登録時に例外処理に入りました。エラーメッセージ: ' . $exception->getMessage());
                continue;
              }
              // テーブル登録
              $new_tweet->fill($tweetlist)->save();
              // 更新処理(=新規登録ではないため)
            } else {
              Log::debug('ID: ' . $account_id . 'のテーブル情報を更新します');
        
              try {
                // twitter_accountsテーブルの更新
                TwitterAccount::where('account_id', $account_id)->update($requestlist);
                Log::debug('twitter_accountsテーブルを更新しました。');
          
                // twitter_account_new_tweetsテーブルの更新
                TwitterAccountNewTweet::where('account_id', $account_id)->update($tweetlist);
                Log::debug('twitter_account_new_tweetsテーブルを更新しました。');
          
                // アプデしたaccount_idを"アプデ済み配列"へ追加する
                $updateded_accounts[] = $account_id;
                Log::debug('アップデートしたアカウントのID配列: ' . print_r($updateded_accounts, true));
          
              } catch (\Exception $exception) {
                Log::debug('DBデータの更新時に例外処理に入りました。エラーメッセージ: ' . $exception->getMessage());
                continue;
              }
            }
          }
          Log::debug('ページカウントを1増やします。');
          // ページカウントを1増やす
          $page++;
        }
      } finally {
        // 更新日時をアップデートする。
        UpdatedAtTable::where('id', self::UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID)->first()->touch();
        Log::debug('======================');
        Log::debug('アカウント検索を終了します');
        Log::debug('======================');
      }
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
       * ユーザーに変更されることがない"user_id"を指定してフォローする。
       * user_id:必須・フォロー先のアカウントID
       */
      Log::debug('=========================================');
      Log::debug('TwitterController.accounts_follow フォロー');
      Log::debug('=========================================');
      $target_user_id = $request->user_id; // フォロー対象のアカウントのID。
      // $target_user_id = 1044456766241558529; // 削除されているID・テスト用
      
      // APIを叩くためのインスタンスを作成
      $connection = $this->connection_instanse_users($request->token, $request->token_secret);
      
      $twitterRequest = $connection->post('friendships/create', array("user_id" => $target_user_id));
      Log::debug('ID:'.$target_user_id.' にフォローを飛ばしました。');
      
      return response()->json(['result' => $twitterRequest]);
    }
    
    // TODO twitterにはフォロー制限やアプリケーションによるAPIの事項制限がある。
    // ユーザーによって時刻がバラバラであるため、そこに対応させなければならない
  
  
    // =======================================
    // 指定したアカウントのフォローを解除する
    // =======================================
    public function accounts_unfollow(Request $request)
    {
      /* POST friendships/destroy - フォロー解除する
       *
       * ユーザーに変更されることがない"user_id"を指定してフォローする。
       * user_id:必須・フォロー先のアカウントID
       */
      Log::debug('==============================================');
      Log::debug('TwitterController.accounts_unfollow フォロー解除');
      Log::debug('==============================================');
      $target_user_id = $request->user_id; // フォロー対象のアカウントのID。
      // $target_user_id = 1044456766241558529; // 削除されているID・テスト用
      
      // APIを叩くためのインスタンスを作成
      $connection = $this->connection_instanse_users($request->token, $request->token_secret);
      
      $twitterRequest = $connection->post('friendships/destroy', array("user_id" => $target_user_id));
      Log::debug('ID:'.$target_user_id.' のフォローを解除しました。');
      
      return response()->json(['result' => $twitterRequest]);
    }
    
    // TODO twitterにはフォロー制限やアプリケーションによるAPIの事項制限がある。
    // ユーザーによって時刻がバラバラであるため、そこに対応させなければならない
  
  
    // =======================================
    // アカウントと対象アカウントとのフォロー関係を取得
    // =======================================
    public function lookup_follow(Request $request)
    {
      /* GET friendships/lookup - フォロー関係の確認
       *
       * ユーザーに変更されることがない"user_id"を指定。
       * user_id:必須・フォロー先のアカウントID
       */
      Log::debug('==============================================');
      Log::debug('TwitterController.lookup_follow フォロー関係取得');
      Log::debug('==============================================');
      $target_user_id = $request->user_id; // フォロー対象のアカウントのID。
      // $target_user_id = 1044456766241558529; // 削除されているID・テスト用
      
      // APIを叩くためのインスタンスを作成
      $connection = $this->connection_instanse_users($request->token, $request->token_secret);
      
      $twitterRequest = $connection->get('friendships/lookup', array("user_id" => $target_user_id));
      Log::debug('ID'.$target_user_id.' とのフォロー関係を取得します。');
      
      // エラーチェック
      if(isset($twitterRequest['errors'])) {
        Log::debug('result: '. print_r($twitterRequest['errors'], true));
        return '';
      } else if(empty($twitterRequest)) {
        Log::debug('result: null');
        return '';
      }
      
      return response()->json(['result' => $twitterRequest]);
    }
  
  
  
    
    // ================================
    // 自動フォローのON/OFF切り替え
    // ================================
    // わかりやすくtoggleというメソッド名だが本質的にはステータスの上書きにあたる
    // 実装ではON/OFFの切り替えしか起こらないようにはしてある
    public function toggle_auto_follow_flg(Request $request) {
      Log::debug('==============================================================');
      Log::debug('TwitterController.toggle_auto_follow_flg 自動フォローフラグON/OFF');
      Log::debug('==============================================================');
      
      // 認証ユーザーを取得
      $user = Auth::user();
      
      Log::debug($request);
      // 「現在の」自動フォローフラグを1or0で取得
      $auto_follow_flg = $request->follow_flg;
      
      // 現時点でON(follow_flg === 1)の時
      if($auto_follow_flg) {
        Log::debug('ID:'.$user->id.'、'.$user->name.'さんの自動フォローをOFFにします');
        $user->auto_follow_flg = false;
        $user->update();
      }else{
        Log::debug('ID:'.$user->id.'、'.$user->name.'さんの自動フォローをONにします');
        $user->auto_follow_flg = true;
        $user->update();
      }
      return response(200);
    }
    
    
    // ===========================
    // 自動フォローを行う
    // ===========================
    /* 流れ
     * ① DBからauto_follow_flgがtrueのUserを取得する
     * ② twitter_accountsテーブルに格納されているIDを取得する
     */
    public function auto_follow(Request $request) {
      Log::debug('=============================');
      Log::debug('auto_follow 自動フォロー');
      Log::debug('=============================');
      
      // -------------------------------------------
      // ① DBからauto_follow_flgがtrueのUserを取得する
      // -------------------------------------------
      // 自動フォローON、削除フラグfalseのUserを全て取得する
      $auto_follow_users = User::where('auto_follow_flg', true)->where('delete_flg', false)->get();
      
      // 1人でもONにしているユーザーがいたら処理を実行
      if($auto_follow_users->isNotEmpty()) {
        Log::debug('自動フォロー処理を開始します');
  
        // ----------------------------------------
        // ② twitter_accountsテーブルの格納IDを取得する
        // ----------------------------------------
        // foreachで1人1人ごとに、twitter_accountsの各登録IDにフォローをかける。
        foreach ($auto_follow_users as $user) {
          Log::debug($user->name.' のフォロー処理を開始...');
 
          
          // 各ユーザーのトークン・シークレットを取得
          $token = $user->token;
          $token_secret = $user->token_secret;
          // インスタンス作成
          $connection = $this->connection_instanse_users($token, $token_secret);
          
          
          // 各ユーザーのフォロー済みIDとtwitter_accountsテーブルを比較し、既にフォロー済みのものはスルーする
          // APIリクエストの回数節約にもなる
          $this->get_account_follow_ids()
          
          
          
        }
        
        
        
        
      } else {
        Log::debug('自動フォローをONにしているユーザーはいませんでした。');
      }
      
    }
  
  
  
  
  
  
    // =======================================
    // アカウントがフォローしているユーザーを取得する
    // =======================================
    private function get_account_follow_ids($user_id, $token, $token_secret) {
      /*
       * friends/ids
       * フォローしているゆーざーの一覧をIDで取得する
       */
      Log::debug('============================================================');
      Log::debug('TwitterController.get_account_follow_ids フォロー中ユーザーの取得');
      Log::debug('============================================================');
      $target_user_id = $user_id; // フォロー対象のアカウントのID。
      // $target_user_id = 1044456766241558529; // 削除されているID・テスト用
      
      // インスタンスを作成
      $connection = $this->connection_instanse_users($token, $token_secret);
  
      // APIを使用し、フォローしているユーザーのID一覧を取得
      $twitterRequest = $connection->post('friendships/ids', array("user_id" => $target_user_id));
      Log::debug('ID'.$target_user_id.' がフォローしているユーザーを取得します。');
      Log::debug('一覧: '. print_r($twitterRequest, true));
  
      return $twitterRequest;
    }
    
    
    
    // ===========================================================
    // 指定したtwitterアカウントIDをfollowsテーブルに登録
    // ===========================================================
    public function add_table_follows(int $account_id, int $target_id) {
      Log::debug('==========================================================');
      Log::debug('TwitterController.add_table_follows アカウントをfollowsに登録');
      Log::debug('==========================================================');
      // 数値以外の値が入らないよう、引数はintを指定する。
      // カラムは丸め込み対策でvarchar(255)で作成しているため、配列作成時にキャストする
      $data = [
          'account_id' => (string)$account_id,
          'follow_target_id' => (string)$target_id
      ];
      // テーブルに既に存在していない場合は新規登録する
      $user = TwitterAccountFollow::firstOrCreate($data);
      if($user->exists) {
        Log::debug('既に登録済みのアカウントでした');
      }else{
        Log::debug('ID:'.$account_id.'が、ID:'.$target_id.'をフォローしているという情報をDBに保存しました。');
      }
    }
    
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
      // API keyなどを定義・エイリアスにするか検討
      $consumer_key = config('services.twitter')['client_id'];
      $consumer_secret = config('services.twitter')['client_secret'];
      $access_token = config('services.twitter')['access_token'];
      $access_token_secret = config('services.twitter')['access_token_secret'];
      
      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
      
      return $connection;
    }
    // =======================================
    // TODO アプリの制限回数確認
    // =======================================
    // 制限確認用のメソッド
    // これで残りのAPI回数を算出し、15/15minや1000/1day制限に掛からないように処理を分ける
  
    public function check_limit_status()
    {
      // API keyなどを定義・エイリアスにするか検討
      $consumer_key = config('services.twitter')['client_id'];
      $consumer_secret = config('services.twitter')['client_secret'];
      $access_token = config('services.twitter')['access_token'];
      $access_token_secret = config('services.twitter')['access_token_secret'];
  
      $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
      
      $twitterRequest = $connection->get('application/rate_limit_status');
      
      return response()->json(['result' => $twitterRequest]);
    }
}
