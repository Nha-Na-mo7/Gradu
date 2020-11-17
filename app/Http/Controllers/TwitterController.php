<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Brand;
use App\Models\TweetCountDay;
use App\Models\TweetCountHour;
use App\Models\TweetCountWeek;
use App\Models\TwitterAccount;
use App\Models\FollowApiLimit;
use App\Models\FollowTarget;
use App\Models\TwitterAccountNewTweet;
use App\Models\UpdatedAtTable;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
// use mysql_xdevapi\Exception;

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
    // APIの使用が変更された場合はこちらの数値を変更してください
  
    // twitterAPIが1日にフォローできる最大数。
    // ただし1日に1回のリセットだとフォローに偏りが出やすくなるので、
    // 処理では1/2した12時間ごとにフォローカウントをリセットするようにしている。
    const DAILY_LIMIT_FOLLOW = 400;
    // twitterAPIが15分の間にフォローできる最大数。
    const MIN_LIMIT_FOLLOW = 15;
    
    // search/tweetsAPIが15分間の間に検索できる最大数。誤差でAPI制限に
    const SEARCH_TWEETS_LIMIT = 440;
    
    // updated_at_tablesにおけるtwitter_accountsを参照するテーブルのID
    const UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID = 1;
    
    
    /*
     * バッチ処理の流れ1 - Twitterユーザー編 -
     * 1, 1日1回、指定時刻になったら、Laravelスケジューラを起動しバッチ処理を開始する
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
     */

    
    // ==================================
    // Twitterアカウント検索 ※バッチ処理
    // ==================================
    public function search_accounts()
    {
      Log::debug('==============================================');
      Log::debug('TwitterController.search_accounts アカウント検索');
      Log::debug('==============================================');
      
      // --------------------------
      // APIリクエストの前準備
      // --------------------------
  
      $query = 'ウェブカツ'; // 検索キーワード
      $count = 20; // 1回の取得件数
      $page = 1; // 検索ページ。これを終わるまで繰り返す。
      
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
        $updated_model = UpdatedAtTable::where('id', self::UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID)->first();
        $now = Carbon::now();
        $updated_model->fill(['updated_at' => $now])->save();
        Log::debug('======================');
        Log::debug('アカウント検索を終了します');
        Log::debug('======================');
        exit();
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
    // 指定したアカウントをフォローする(ボタンから)
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
      // ログイン中のユーザー情報を取得
      $user = Auth::user();
      // 処理をするユーザーのTwitterID
      $account_id = $user->twitter_id;
      // トークン・シークレット
      $token = $user->token;
      $token_secret = $user->token_secret;
      // フォロー対象のアカウントのID。
      $target_user_id = $request->user_id;
      // 削除されているID・テスト用
      // $target_user_id = 1044456766241558529;
  
      // API制限チェック
      Log::debug('API制限のチェックを行います。');
      $check_limit15 = $this->api_limit_check_15min($account_id);
      $check_limit_day = $this->api_limit_check_day($account_id);
      
      // 制限チェックのどちらかに引っかかった場合、フォロー処理はせずに終了する
      if($check_limit15 && $check_limit_day) {
        Log::debug('accounts_follow: API制限チェックOKでした。');
      }else{
        Log::debug('accounts_follow: システム上のAPI制限に引っかかったため、フォローはせずに終了します。');
        return response()->json(['error' => 'フォロー制限がかかっています。しばらくお待ちください'], 200);
      }
      
      // APIリクエスト用のインスタンスを作成
      $connection = $this->connection_instanse_users($token, $token_secret);
      
      // フォローリクエストを飛ばす
      $twitterRequest = $connection->post('friendships/create', array("user_id" => $target_user_id));
      Log::debug('ID:'.$target_user_id.' にフォローを飛ばしました。');
  
      // フォローの成功失敗を問わず、APIにリクエストを飛ばしたのでカウントは更新する
      $this->increment_follow_count($account_id);
      
      // フォロー成功したら、followsテーブルにフォローしたアカウントIDを登録する
      if ($connection->getLastHttpCode() === 200) {
        Log::debug('followsテーブルにフォローしたIDを登録します。');
        $this->add_table_follows($account_id, $target_user_id);
      } else {
        // 何らかのリクエストエラーが起きた時
        Log::debug('APIリクエストエラー: '. print_r($twitterRequest, true));
      }
      Log::debug('=======================================================');
      Log::debug('▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲ 手動フォローを終了します。 ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲');
      Log::debug('=======================================================');
      return response()->json(['result' => $twitterRequest], 200);
    }
    
  
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

    // =======================================
    // アカウントと対象アカウントとのフォロー関係を取得
    // =======================================
    public function lookup_follow($account_id, $token, $token_secret)
    {
      /* GET friendships/lookup - フォロー関係の確認
       *
       * ユーザーに変更されることがない"user_id"を指定。
       * user_id:必須・フォロー先のアカウントID
       */
      Log::debug('==============================================');
      Log::debug('TwitterController.lookup_follow フォロー関係取得');
      Log::debug('==============================================');
      $target_user_id = $account_id; // フォロー対象のアカウントのID。
      // $target_user_id = 1044456766241558529; // 削除されているID・テスト用
      
      // APIを叩くためのインスタンスを作成
      $connection = $this->connection_instanse_users($token, $token_secret);
      
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
    
    
    // =================================
    // 自動フォロー(AUTO FOLLOW) ※バッチ処理
    // =================================
    /* 処理の流れ
     * ① Userテーブルからauto_follow_flgがtrueのUserを取得する。誰もいなければ終了。
     * ② twitter_accountsテーブルに格納されているIDを全て取得し、フォロー候補として配列に格納。
     *
     * ③ foreachを使って、①で取得したaccountsを回す
     * ④ 処理中のアカウントのフォローリストを取得する
     * ⑤ ②と④を比較し、既にフォロー済みのアカウントを除外した未フォローリストを作成する
     * ⑥ ⑤で作成した未フォローリストをforeachで回し、1人ずつフォローを飛ばす。1度の処理で最大4人まで。
     *
     * ⑦ ③~⑥を繰り返す。全てのユーザーの未フォローリストが空になる or その回のリクエスト制限回数を超えたら終了。
     *
     * API制限について
     * 現行のTwitterAPIは 400/1day、15/15minの制限がある。
     * 30分ごとに実行するバッチなので一度にフォローさせてもいいが、ユーザー側も手動フォローなどしてAPI規制にかかりやすくなっても困るため、1日の最大人数に応じた処理で行う。
     * ユーザーが手動でフォローすることを考えると、ギリギリラインでフォローさせるとユーザーにAPI制限がかかり利便性に難が出る場合がある。
     * 30分に5人ペース → 240人/1dayペースでフォロー可能
     */
    public function auto_follow() {
      Log::debug('====================================');
      Log::debug('TwitterController auto_follow 自動フォロー');
      Log::debug('====================================');
      
      // -------------------------------------------
      // ① DBからauto_follow_flgがtrueのUserを取得する
      // -------------------------------------------
      // 自動フォローON、削除フラグfalseのUserを全て取得する
      Log::debug('auto_follow_flg:true、delete_flg:falseのUserを全て取得します。');
      $auto_follow_users =
          User::where('auto_follow_flg', true)
          ->where('delete_flg', false)
          ->get();
      
      // 1人でもONにしているユーザーがいたら処理を実行
      if ($auto_follow_users->isNotEmpty()) {
        Log::debug('該当ユーザーがいたので自動フォロー処理を開始します');
        
        // ----------------------------------------
        // ② twitter_accountsテーブルの格納IDを取得する
        // ----------------------------------------
        // twitter_accountsテーブルのaccount_idカラムを全件取得
        $accounts = TwitterAccount::select('account_id')->get();
        
        // 自動フォロー対象候補のaccount_idを格納する配列
        $candidate_accounts = [];
        
        // 何らかの理由でaccount_idが0件取得になってしまった場合、自動フォロー以前にフォロー先がないので処理終了。
        if ($accounts->isNotEmpty()) {
          // 取得したaccount_idを1つずつ、自動フォローの候補配列に詰める
          Log::debug('候補となるaccount_idが$accountsに存在します。1つずつ$candidate_accountsに詰めます。');
          foreach ($accounts as $account) {
            $candidate_accounts[] = $account->account_id;
          }
          // Log::debug('今回フォローする対象となるIDは $candidate_accounts: '.print_r($candidate_accounts, true));
        } else {
          Log::debug('候補アカウントがなくフォロー先もいないので処理を終了します。');
          exit();
        }
  
        // ---------------------------------
        // ③ foreachで1人ずつフォロー処理
        // ---------------------------------
        foreach ($auto_follow_users as $user) {
          Log::debug($user->name.' さんのフォロー処理中');
  
          // 各ユーザーのtwitterIDを取得
          $user_twitter_account_id = $user->twitter_id;
          
          // twitterトークンを取得
          $token = $user->token;
          $token_secret = $user->token_secret;
          
          // ----------------------------------------
          // ④ アカウントのフォローリストを取得する
          // ----------------------------------------
          // このユーザーがフォローしているアカウントIDを格納する配列
          $follow_list = [];
          
          // 自動フォロー開始時までにフォローが行われている場合、テーブルデータとズレが生じるため直接フォローリストを確認。
          Log::debug($user->name.'さんのフォローリストを取得します。');
          $follow_ids = $this->get_account_follow_ids($user_twitter_account_id, $token, $token_secret);
          
          // 1つずつ配列に格納する
          if (!empty($follow_ids->ids)){
            Log::debug('$userがフォローしているアカウント一覧を$follow_listに格納しています');
            foreach ($follow_ids->ids as $id) {
              $follow_list[] = $id;
            }
          }
          // TODO 最新のフォロー済みアカウントリストはどのタイミングでfollowテーブルに格納する？
          
          // ----------------------------------------------------
          // ⑤ フォロー済みのアカウントを除外した未フォローリストを作成する
          // ----------------------------------------------------
          // 既にフォロー済みのアカウントを除外することで、APIリクエストの回数節約にもなる
          
          // ②の配列から、④の配列に存在するaccount_idを除外した、未フォローリストを配列として作成
          $target_account_ids = array_diff($candidate_accounts, $follow_list);
          Log::debug('未フォローリストを作成しました。');
          
          // 未フォローリストが空なら全員フォロー済みなので、次のユーザーの処理に移行。
          if (empty($target_account_ids)) {
            Log::debug('$target_account_idsが空です。全員フォロー済みなので'.$user->name.'さんの処理は終了します。');
            continue;
          }
          
          // ---------------------------------------------------------------------
          // ⑥ 未フォローリストをforeachで回し、1人ずつフォローを飛ばす。1度の処理で最大5人まで。
          // ---------------------------------------------------------------------
          // TwitterAPI用のインスタンス作成
          $connection = $this->connection_instanse_users($token, $token_secret);
          
          // 処理した回数のカウント
          $roop_count = 0;
          
          Log::debug('ここから未フォローリストから最大4人をフォローする処理に入ります。');
          foreach ($target_account_ids as $target_account_id) {
            // 1度の処理で5人までのフォローをするので、6回目の処理が始まっていたらbreakする
            // ここに判定を書く理由は、後続でフォロー時にAPIリクエストエラーが起きた時でも対応できるようにするため。
            $roop_count++;
            Log::debug('▼▼▼▼▼'.$user->name.'さんの'.$roop_count.'回目のループフォローリクエスト処理です。');
            
            // ループカウントチェック
            if ($roop_count > 5) {
              Log::debug($roop_count.'回目の処理はAPI制限対策で行いません。breakします。');
              break;
            }
            
            // API制限チェック
            $check_limit15 = $this->api_limit_check_15min($user_twitter_account_id);
            $check_limit_day = $this->api_limit_check_day($user_twitter_account_id);
            // 制限チェックのどちらかに引っかかった場合、そのユーザーの処理はスキップされる
            if($check_limit15 && $check_limit_day) {
              Log::debug('API制限チェックOKでした。');
            }else{
              Log::debug('システム上のAPI制限に引っかかったため、このユーザーの処理を終了します。');
              break;
            }
            
            // フォローリクエストを飛ばす
            $twitterRequest = $connection->post('friendships/create', array(
                "user_id" => $target_account_id
            ));
            Log::debug('ID:'.$target_account_id.' にフォローを飛ばしました。');
            
            
            // フォローの成功失敗を問わず、APIにリクエストを飛ばしたのでカウントは更新する
            $this->increment_follow_count($user_twitter_account_id);
            
            
            // フォロー成功したら、followsテーブルにフォローしたアカウントIDを登録する
            if ($connection->getLastHttpCode() === 200) {
              Log::debug('followsテーブルにフォローしたIDを登録します。');
              $this->add_table_follows($user_twitter_account_id, $target_account_id);
              Log::debug($roop_count.'回目のループ処理が完了しました。続投します。▲▲▲▲▲');
            } else {
              // 何らかのリクエストエラーが起きた時は、処理中のユーザーの処理を中断する
              Log::debug('APIリクエストエラー: '. print_r($twitterRequest, true));
              Log::debug($user->name.'さんの処理を終了し、次の人の処理に移行します。');
              break;
            }
          }
          Log::debug($user->name.'さんの今回のフォロー処理が終了しました。次のユーザーの処理に移行します。');
        }
        Log::debug('[!]全てのユーザーの自動フォロー処理が完了しました。');
        
        
      // 自動フォローをONにしているユーザーがいなかった時
      } else {
        Log::debug('自動フォローをONにしているユーザーはいませんでした。処理を終了します。');
      }
      Log::debug('===========================================================');
      Log::debug('▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲ 自動フォローを終了します。 bye ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲');
      Log::debug('===========================================================');
    }
  
    
    // =======================================
    // アカウントがフォローしているユーザーを取得する
    // =======================================
    private function get_account_follow_ids($account_id, $token, $token_secret) {
      /*
       * GET friends/ids
       * フォローしているアカウント一覧をIDで取得する
       * 15/15min制限あり
       * アプリケーション認証では対象のアカウントが非公開になっていた場合フォローリストを取得できないため、ユーザー認証させる
       */
      Log::debug('============================================================');
      Log::debug('TwitterController.get_account_follow_ids フォロー中ユーザーの取得');
      Log::debug('============================================================');
      $target_account_id = $account_id; // 確認したいアカウントのID。
      // $target_user_id = 1044456766241558529; // 削除されているID・テスト用
      
      // インスタンスを作成
      $connection = $this->connection_instanse_users($token, $token_secret);
  
      // APIを使用し、フォローしているユーザーのID一覧を取得
      $twitterRequest = $connection->get('friends/ids', array("user_id" => $target_account_id));
      Log::debug('ID'.$target_account_id.' がフォローしているユーザーを取得します。');
      // Log::debug('一覧: '. print_r($twitterRequest, true));
  
      return $twitterRequest;
    }
    
    
    
    // ===========================================================
    // 指定したtwitterアカウントIDをfollowsテーブルに登録
    // ===========================================================
    public function add_table_follows(int $account_id, int $target_id) {
      Log::debug('==========================================================');
      Log::debug('TwitterController.add_table_follows アカウントをfollowsに登録');
      Log::debug('==========================================================');
      // TODO 使用目的 登録するアカウントIDはtwitter_accountsに記述のあるものだけで良い、全部載せると膨大な量になる。
      
      // 数値以外の値が入らないよう、引数はintを指定する。
      // カラムは丸め込み対策でvarchar(255)で作成しているため、配列作成時にキャストする
      $data = [
          'account_id' => (string)$account_id,
          'follow_target_id' => (string)$target_id
      ];
      // テーブルに存在していない場合は新規登録する
      try {
        $user = FollowTarget::firstOrCreate($data);
        if($user->wasRecentlyCreated) {
          Log::debug('ID:'.$account_id.'が、ID:'.$target_id.'をフォローしているという情報をDBに保存しました。');
        }else{
          Log::debug('既に登録済みでした。 ID:'.$account_id.'=> フォロー => ID:'.$target_id);
        }
        return response(200);
      }catch (\Exception $exception) {
        Log::debug('DBへの登録or更新時にエラー発生: '. $exception->getMessage());
        return response(500);
      }
    }
    
    // ===========================================================
    // 指定したtwitterアカウントIDをfollowsテーブルから削除
    // ===========================================================
    public function delete_table_follows($account_id, $target_id) {
      Log::debug('==========================================================');
      Log::debug('TwitterController.delete_table_follows アカウントをfollowsから削除');
      Log::debug('==========================================================');
      try {
        FollowTarget::where('account_id', $account_id)
            ->where('follow_target_id', $target_id)
            ->delete();
  
        Log::debug('ID'.$account_id.'のフォローリストからID:'.$target_id.'を削除しました。');
        return response(200);
        
      }catch (\Exception $exception) {
        Log::debug('レコードの削除時にエラー発生: '. $exception->getMessage());
        return response(500);
      }
    }
    
    // ==============================================
    // Twitterユーザーが15/15min制限にかかっていないかの判定
    // ==============================================
    // フォロー処理の前に呼び出し、API制限がかかっていた場合などはfalseを返して処理に入らないようにする。
    public function api_limit_check_15min($account_id) {
      Log::debug('==========================================================');
      Log::debug('TwitterController.api_limit_check_15min 15/15minのフォロー制限確認');
      Log::debug('==========================================================');
  
      // 時刻
      $now = new Carbon('now');
      $plus15min = new Carbon('+15 minutes');
      $plus12hours = new Carbon('+12 hours');
      
      // ユーザーのアカウントIDでAPI制限テーブルからレコードを取得する
      // ただし新規作成されるなどした場合は、現在時刻+15分、12時間後で値をセットする
      Log::debug($account_id.'のAPILimitを取得します。');
      $account_limit_data = FollowApiLimit::firstOrCreate([
          'account_id' => $account_id
      ], [
          'day_follow_limit_time' => $plus12hours,
          'fifteen_min_follow_limit_time' => $plus15min
      ]);
      
      // 現在時刻と15分制限時間を比較する。
      // 時間内ならば15/15min制限をオーバーしていないか、フォロー回数を確認する
      if($account_limit_data->fifteen_min_follow_limit_time > $now) {
        Log::debug('15/15minの制限時間内でした。フォロー回数を確認します。');
        
        // 15分以内でのフォロー回数を確認して、制限MAXに到達していた場合はfalseを返却する
        if($account_limit_data->fifteen_min_follow_count < self::MIN_LIMIT_FOLLOW) {
          Log::debug('フォロー回数: '.$account_limit_data->fifteen_min_follow_count.'/'.self::MIN_LIMIT_FOLLOW.'回');
          Log::debug('15/15minの制限回数以内です。フォローリクエストOKです。時間のセットはありません。');
         return true;
        }else{
          Log::debug('フォロー回数: '.$account_limit_data->fifteen_min_follow_count.'/'.self::MIN_LIMIT_FOLLOW.'回');
          Log::debug('15/15minのフォローリクエスト上限に到達しています。しばらくお待ちください。');
          return false;
        }
      // 現在時刻がAPI制限の時間を超えていた場合、15分以内のフォローカウントを0にし、15分後の時間を再セットする。
      }else{
        Log::debug('15/15min制限時間を過ぎています。フォローリクエストOKです。新しく時間をセットし、フォローカウントをリセットします。');
        $data = array(
            'fifteen_min_follow_count' => 0,
            'fifteen_min_follow_limit_time' => $plus15min
        );
        $account_limit_data->fill($data)->save();
        Log::debug('15分後にセットしました。');
        return true;
      }
    }
    
    // ==============================================
    // Twitterユーザーが400/day制限にかかっていないかの判定
    // ==============================================
    // 上記の15/15minの400/dayバージョン。
    // フォロー処理の前に呼び出し、API制限がかかっていた場合などはfalseを返して処理に入らないようにする。
    //
    // ただし1日400件のフォローカウントを24時間に1度しかリセットしなかった場合、偏りが出る可能性がある。
    // (例:00:00にフォローカウントリセットしても、21:00に400件、03:00に400件フォローのリクエストを送った場合に6時間で800リクエストがあるように見えてしまう)
    // こうしたリスクを少しでも分散するために12時間ごとにフォローリクエスト200件までとして処理を行う。
    public function api_limit_check_day($account_id) {
      Log::debug('==========================================================');
      Log::debug('TwitterController.api_limit_check_day 400/1dayのフォロー制限確認');
      Log::debug('==========================================================');
      
      // 時刻
      $now = new Carbon('now');
      $plus15min = new Carbon('+15 minutes');
      $plus12hours = new Carbon('+12 hours');
      
      // ユーザーのアカウントIDでAPI制限テーブルからレコードを取得する
      // ただし新規作成されるなどした場合は、現在時刻+15分、12時間後で値をセットする
      Log::debug($account_id.'のAPILimitを取得します。');
      $account_limit_data = FollowApiLimit::firstOrCreate([
          'account_id' => $account_id
      ], [
          'day_follow_limit_time' => $plus12hours,
          'fifteen_min_follow_limit_time' => $plus15min
      ]);
      
      // 現在時刻と1日の制限時間を比較する。
      // 時間内ならば、400/1day制限をオーバーしていないかどうかフォロー回数を確認する
      if($account_limit_data->day_follow_limit_time > $now) {
        Log::debug('400/1dayの制限時間内でした。フォロー回数を確認します。');
        
        // 12時間以内でのフォロー回数を確認して、制限MAXに到達していた場合はfalseを返却する
        if($account_limit_data->day_follow_count < (self::DAILY_LIMIT_FOLLOW / 2) ) {
          Log::debug('フォロー回数: '.$account_limit_data->day_follow_count.'/'.(self::DAILY_LIMIT_FOLLOW / 2).'回');
          Log::debug('400/1day制限回数以内です。フォローリクエストOKです。時間のセットはありません。');
          return true;
        }else{
          Log::debug('フォロー回数: '.$account_limit_data->day_follow_count.'/'.(self::DAILY_LIMIT_FOLLOW / 2).'回');
          Log::debug('400/1dayのフォローリクエスト上限に到達しています。しばらくお待ちください。');
          return false;
        }
        // 現在時刻がAPI制限の時間を超えていた場合、400/1dayフォローカウントを0にし、12時間後の時間を再セットする。
      }else{
        Log::debug('400/1day制限時間を過ぎています。フォローリクエストOKです。新しく時間をセットし、フォローカウントをリセットします。');
        $data = array(
            'day_follow_count' => 0,
            'day_follow_limit_time' => $plus12hours
        );
        $account_limit_data->fill($data)->save();
        Log::debug('12時間後にセットしました。');
        return true;
      }
    }
    
    
    // =======================================
    // API制限テーブルのフォローカウントを増やす
    // =======================================
    public function increment_follow_count($account_id) {
      Log::debug('==========================================================');
      Log::debug('TwitterController.increment_follow_count フォローカウントを増やす');
      Log::debug('==========================================================');
      $account_limit_data = FollowApiLimit::where('account_id', $account_id)->first();
      Log::debug('FollowApiLimitモデルを取得します');
      
      // カウントを1増やす
      $account_limit_data->day_follow_count += 1;
      $account_limit_data->fifteen_min_follow_count += 1;
      Log::debug('dayカウントを増やしました。day_follow_count:'.$account_limit_data->day_follow_count);
      Log::debug('15minカウントを増やしました。15_min_follow_count:'.$account_limit_data->fifteen_min_follow_count);
      
      // 最後に保存
      $account_limit_data->save();
      Log::debug('DBへのセーブが完了しました');
    }
    
    
    // ==============================================
    // 仮想通貨や通貨ごとのツイート数を集計する
    // ==============================================
    /* 処理の流れ
     * ❶ 現在の日付でレコードがあるのかを呼び出す
     * ① brandsテーブルのレコードを全て取得する
     * ② 取得した各レコードの「銘柄名(アルファベット)」「カタカナ名」の2つを組み合わせた検索ワードを作成し、配列に1つずつ格納する
     * ＞(例:'"BTC" OR "ビットコイン"')
     *
     * ③ ②の配列を回し、1つずつsearch/tweetsで検索をかける
     * ④ その検索ワードを全件検索し終える、またはAPI制限上限に到達するまでループしながら検索をかける
     * ⑤ 取得できたツイートの総数をカウントする
     * ⑥ 1度の検索で全件取得しきれなかった場合、レスポンスに付与されたnext_resultsのURLを使って再度検索を行う
     *
     * ⑦ ④〜⑥を繰り返し、期間内の取得ツイートが無くなったらfor文ループを抜ける
     *
     * 途中でAPI制限にかかった場合、15分の待機をした上で同条件でもう一度検索を開始する。
     * アプリケーション認証では、15分間の間に450回のツイート検索ができる。
     */
    public function count_tweets($search_type = 0) {
    // public function count_tweets($sub_days = 2) {
      Log::debug('==============================================');
      Log::debug('TwitterController.count_tweets 銘柄別ツイート数取得');
      Log::debug('==============================================');
      
      // 1時間以内のツイート全て・24時間以内のツイート全てを集計する。
      // 1週間以内のツイートに関しては過去1日以内のツイート×7日分の合計値で算出する
      // ・APIの仕様で、7日間の期間指定をしたとしても取得できなくなる場合がある
      // ・毎時毎時7日分のツイート検索を行うのはサービスとしても負荷が大きい
      // ・一回でも7日分のツイート数さえ取得してしまえば、後は1日以内のツイートを算出すればよくなり負荷が軽くなる。余計なAPI制限にかかってエラーを起こす危険も減少する。
      
      // searchType 0:1時間以内のみ 1:過去1日間に検索をかける
      // TODO テスト用。
      // $search_type = 1;
      
      // 検索再開フラグ
      $resume_flg = false;
      
      // API制限などで検索が続行できなくなった場合のフラグ
      $limit_flg = false;
      
      // リクエスト回数カウンター
      $request_count = 0;
      // 検索ワード用の配列
      $search_words = [];
      // 通貨ID
      $brand_id = 0;
      // since(この時間以降のツイートに絞って検索をかける)
      $since_date = '';
      // until(検索を開始した時点での現在時刻。)
      $until_date = '';
      // 次ページ用のパラメータ
      $next_results = '';
  
      $tweet_count = 0;
      $tweet_count_day = 0;
  
  
      // --------------------------------------------------
      // ❶ 現在の日付時刻でレコードがあるかを呼び出す
      // --------------------------------------------------
      // 現在時刻
      $now = CarbonImmutable::now();

      // 処理が完了しているかを記録するテーブルのモデルインスタンス
      $updated_tweet_count = '';
  
      // 検索開始時刻に既に更新が行われているかを確認する
      switch ($search_type) {
        case 0:
          $search_time = $now->format('Y-m-d H:');
          Log::debug('$search_time:'.$search_time);
          // 時間テーブルを確認する。%を付与してLIKE検索をする。
          $Updated_tweet_count = UpdatedAtTable::where('table_name', 'tweet_count_hours')
              ->where('updated_at', 'LIKE', "$search_time%")
              ->first();
          break;
        case 1:
          // テーブル検索用、その日の集計が完了しているかの判定なので検索は日付だけでOK
          $sub_now = $now->subDays($sub_days - 1);
          $search_time = $sub_now->format('Y-m-d');
          Log::debug('$search_time:'.$search_time);
          // 日テーブルを確認する。%を付与してLIKE検索をする。
          $Updated_tweet_count = UpdatedAtTable::where('table_name', 'tweet_count_days')
              ->where('updated_at', 'LIKE', "$search_time%")
              ->first();
          break;
      }
  
      // 既にその時間のレコードが存在する場合、complete_flgをチェックし、その時間の更新が完了しているかを確認する
      if(isset($Updated_tweet_count)) {
        // 完了している場合
        if($Updated_tweet_count->complete_flg){
          Log::debug('この時間の集計は完了しています。');
          Log::debug('▲▲▲▲▲▲▲ 通貨ツイート検索を終了します。 bye ▲▲▲▲▲▲▲');
          Log::debug('============================================');
          exit();
        // 中断されていた場合
        }else{
          Log::debug('集計の途中で中断されているようです。IDと集計開始時刻を取得し、再開します。');
          // 再開フラグをtrueに
          $resume_flg = true;
          
          
          // 最終更新日時を取得しStringで格納
          $until_date = new CarbonImmutable($Updated_tweet_count->updated_at);
          $until_date_insert_db_format = $until_date;
          
          Log::debug('$until_date:'.$until_date);
          Log::debug('$until_date(=until_date_insert_db_format):'.$until_date);

        }
      }else{
        Log::debug('集計が未実施です。');
        // 1時間ごとならid2、1日ならid3のテーブルを参照する
        $table_id = $search_type == 0 ? 2 : 3;
        $Updated_tweet_count = UpdatedAtTable::where('id', $table_id)->first();
        // その時間のcomplete_flgをfalseに変更
        $Updated_tweet_count->fill([
           'complete_flg' => false,
        ])->save();
      }
      
      
      // 引数の検索条件によって、sinceを分ける(0: -1hour、1: -1day)
      // さらに、対応するTweetCountモデルを取得する
      Log::debug('遡って取得する時間を決めます。');
      switch ($search_type){
        case 0:
          Log::debug('$search_type :'.$search_type.'(= hour)です。1時間前の時刻を取得します。');
          $subHour = $now->subHour();
          $since_date = $subHour;
  
          // 検索用・DB用にフォーマットを修正
          $until_date = $now->format('Y-m-d_H:i:s_\J\S\T');
          $since_date = $since_date->format('Y-m-d_H:i:s_\J\S\T');
          
          // 続きからの場合
          if($resume_flg) {
            // 更新されている項目がある場合、complete_flgは必ずfalseとなっている項目が存在するため、取得できる
            $tweetCountModel = TweetCountHour::where('complete_flg', false)->first();
  
            // 更新途中の銘柄IDを取得する
            $restarting_brand_id = $tweetCountModel->brand_id;
            // 途中までのカウントを取得する
            $tweet_count = $tweetCountModel->tweet_count;
            // type:0なので1時間前の時刻を取得する
            $since_date = (new CarbonImmutable($Updated_tweet_count->updated_at))->subHour();
            // 検索再開地点のnext_resultsパラメータを取得する
            $next_results = $tweetCountModel->next_results;
            // $next_results = preg_replace('/^\?/', '', $next_results);
            // Log::debug('next_resultsから先頭の?を取り除きました。 next_results:'.$next_results);
          }else{
            $until_date_insert_db_format = $now;
          }
          
          break;
        case 1:
          Log::debug('$search_type :'.$search_type.'(= day)です。前日の00:00~00:00までの時刻を取得します。');
          // n日前の時刻を取得する 例えば$sub_daysが2なら...
          $today = CarbonImmutable::today(); // 2020-10-03 00:00:00
          $until_date = $today->subDays($sub_days - 1);//2020-10-02 00:00:00
          $since_date = $today->subDays($sub_days); // 2020-10-01 00:00:00
  
          // 検索用・DB用にフォーマットを修正
          $until_date = $until_date->format('Y-m-d_H:i:s_\J\S\T'); //2020-10-02_00:00:00_JST
          $since_date = $since_date->format('Y-m-d_H:i:s_\J\S\T'); //2020-10-01_00:00:00_JST
        
          // 続きからの場合
          if($resume_flg) {
            $tweetCountModel_day = TweetCountDay::where('complete_flg', false)->first();
            
            // 更新途中の銘柄IDを取得する
            $restarting_brand_id = $tweetCountModel_day->brand_id;
            // 途中までのカウントを取得する
            $tweet_count_day = $tweetCountModel_day->tweet_count;
            // // type:1なので1日前の時刻を取得する
            // $since_date = $final_updated->subDay();
            // 次のパラメータを取得する
            $next_results = $tweetCountModel_day->next_results;
            // $next_results = preg_replace('/^\?/', '', $next_results);
            // Log::debug('next_resultsから先頭の?を取り除きました。 next_results:'.$next_results);
            Log::debug('❶--$until_date_insert_db_format:'.$until_date_insert_db_format);
            
          }else{
            $until_date_insert_db_format = $today->subDays($sub_days - 1); //2020-10-02 00:00:00
  
            Log::debug('1-c--$until_date_insert_db_format:'.$until_date_insert_db_format);
          }
          break;
      }
      

      
      // --------------------------------------------------
      // ① brandsテーブル(取り扱う通貨名情報)のレコードを全て取得する
      // --------------------------------------------------
      Log::debug('brandsテーブルから全ての通貨レコードを取得します。');
      $brandsController = new BrandController();
      $all_brands = $brandsController->get_brands();
  
      // 万が一brandsテーブルが空だったとき(普通この処理に辿り着く事は無い)
      if ($all_brands->isEmpty()) {
        Log::debug('検索すべき通貨名が取得数0でした。seederを打ったか確認してください。');
        exit();
      }
      
      // ----------------------------------------------------------------------
      // ② 取得したレコードから、銘柄名とカタカナ名を組み合わせた検索ワードを作り、配列に格納
      // ----------------------------------------------------------------------
      foreach ($all_brands as $brand) {

        // レコードから銘柄名、カタカナ名を取り出す
        $name = $brand->name; // 銘柄名(例: BTC)
        $realname = $brand->realname; // カタカナ名(例: ビットコイン)
        
        // 2つを組み合わせた検索ワードを作る
        $search_word = $name.' OR '.$realname;
        
        // 検索ワード配列に格納する
        $search_words[] = $search_word;
      }
      // Log::debug('検索ワード配列・これを回して1つずつ検索をかけます。'.print_r($search_words, true));
      
      // アプリケーション認証インスタンス作成
      $connection = $this->connection_instanse_OAuth2();
      
      // ---------------------------------------------
      // ③ 検索ワード配列を回し、パラメータを設定する
      // ---------------------------------------------
      foreach ($search_words as $search_word) {
        
        // インクリメントしてbrandsテーブルのidと対応
        $brand_id++;
        
        if(!$resume_flg) {
          // 取得できたツイートの総数変数の初期化
          $tweet_count = 0;
          $tweet_count_day = 0;
        }
        
        // 現在の検索中の通貨IDと、途中まで実施済みの通貨IDを比較
        if($resume_flg){
          Log::debug('再開させる通貨ID:'.$restarting_brand_id.' 現在の検索中通貨ID:'.$brand_id .'3--$until_date_insert_db_format:'.$until_date_insert_db_format);;
          if($restarting_brand_id > $brand_id) {
            Log::debug('既に検索終了済みの通貨です。次の通貨の検索に移行します。');
            continue;
          }else{
            Log::debug('ここから再開します。$tweet_count/$tweet_count_day :'.$tweet_count.' / '.$tweet_count_day);
            Log::debug('3b--$until_date_insert_db_format:'.$until_date_insert_db_format);
          }
        }
        
        // パラメータを記述
        // sinceなどはparams内に記述してもループ中に消失するためベタ書き。
        // 検索ワード(リプライを含まない、RTを含まない、$since_data以降、$until_date以前のツイート検索)
        $params = array(
            'lang' => 'ja', // 地域・日本に限定する
            'count' => '100', // 取得件数。search/tweetsのAPIが一度に取得可能な最大件数は100。
            'result_type' => 'recent', // 最新のツイート
            'q' => $search_word . ' -filter:replies ' . 'exclude:retweets'.' since:'.$since_date. ' until:'.$until_date, // 検索ワード
        );
        
        //続きからの時、next_paramsを追加する
        if($resume_flg) {
          parse_str($next_results, $params);
        }
        
        // ---------------------------------------------------
        // ④ その検索ワードの全件検索し終える、
        //    またはAPI制限上限に到達するまでループしながら検索をかける
        // ---------------------------------------------------
        for (;$request_count < self::SEARCH_TWEETS_LIMIT;) {

          Log::debug('API用の検索パラメータを設定しました: '.print_r($params, true));
          Log::debug('4--$until_date_insert_db_format:'.$until_date_insert_db_format);
          // APIにリクエストを飛ばす
          // Log::debug('検索ワード:'.$params['q'].' / 日時:'.$params['since'].'以降のツイートに絞って検索を行います。');
          $search_tweets = $connection->get("search/tweets", $params);
    
          // 配列に変換
          $result_tweets = json_decode(json_encode($search_tweets), true);
          // Log::debug('展開配列:'.print_r($result_tweets['search_metadata'], true));
          
          // リクエストの結果に関わらず、カウントを1進める
          $request_count++;
          Log::debug('現在のsearch/tweetsへのリクエスト回数: '.$request_count.' / '.self::SEARCH_TWEETS_LIMIT);
          
          // エラーが帰ってきた場合の処理
          if(isset($result_tweets['errors'])) {
            Log::debug('返却された配列内にerrors項目が存在します。内容:'.print_r($result_tweets, true));
            
            // API制限の場合(エラーコード88)
            // 想定外のトラブルでAPI制限をオーバーした時も考慮する
            if($result_tweets['errors'][0]['code'] === 88) {
              Log::debug('code = 88、API制限です。');
            }
            
            $limit_flg = true;
            break;
          }
          
          // エラーが出ていなくても制限回数MAXに到達したら、$limit_flgをtrueにしておく
          if($request_count == self::SEARCH_TWEETS_LIMIT) {
            Log::debug('制限回数MAXに到達したので$limit_flgをtrueにします。この後の処理が完了し次第、本時間のスケジューラは終了です');
            $limit_flg = true;
          }
          // ---------------------------------------------
          // ⑤ 取得ツイートの総数をカウントする
          // ---------------------------------------------
          // statusesにツイートのデータが格納されている = この要素数が取得できたツイート数
          Log::debug(count($result_tweets['statuses']).'件のツイートが取得できました。');
          
          // ツイート総数をカウントする
          if($search_type) {
            // 過去1日のstatusesの総数が取得できたツイート数
            $tweet_count_day += count($result_tweets['statuses']);
            Log::debug($params['q'].'のツイート取得件数の合計:'.$tweet_count_day);
          }else{
            // 過去1時間だけの場合はstatusesの総数が取得できたツイート数になる
            $tweet_count += count($result_tweets['statuses']);
            Log::debug($params['q'].'のツイート取得件数の合計:'.$tweet_count);
          }
  
          Log::debug('5--$until_date_insert_db_format:'.$until_date_insert_db_format);
          // -------------------------------------------------------------
          // ⑥ 1度の検索で全件取得しきれなかった場合、
          // search_metadataのnext_resultにURLが指定されるのでこれをparamsに加えもう一度検索する
          // -------------------------------------------------------------
          // next_resultsがない場合
          if (empty($result_tweets['search_metadata']['next_results']) ){
            Log::debug('検索結果に次のページはありません。'.$search_word.'の検索は終了です。');
            break;
          }
          
          // next_resultsがある場合、先頭の"?"を取り除く
          $next_results = preg_replace('/^\?/', '', $result_tweets['search_metadata']['next_results']);
          Log::debug('next_resultsから先頭の?を取り除きました。 next_results:'.$next_results);
          // ?を取り除いたnext_resultsをパラメータに追加した上でループの頭に戻り、もう一度検索する
          // ⑦ 指定期間内の取得ツイートが無くなったら、for文ループを抜ける
          parse_str($next_results, $params);
          Log::debug('$paramsに$next_resultsの情報を追加しました。ループの先頭に戻ります。');
  
          Log::debug('7--$until_date_insert_db_format:'.$until_date_insert_db_format);
          
          $resume_flg = false;
        }
        
        // ----------------
        // ⑧ DB登録処理
        // ----------------
        Log::debug('for文ループが終了しました。取得した'.$search_word.'のツイート総数をDBに登録します。');
        Log::debug('8--$until_date_insert_db_format:'.$until_date_insert_db_format);
        if($search_type){
          Log::debug($search_word.'の取得ツイート総数は、1日:'.$tweet_count_day);
        }else{
          Log::debug($search_word.'の取得ツイート総数は、1時間:'.$tweet_count);
        }
  
        // DBに登録する
        // 引数の検索条件によって、登録するテーブルが変わる

        // 日付
        if($search_type) {
          if($limit_flg) {
            Log::debug('この通貨は検索が中断されました。next_resultsを記録しておき、breakします。');
            $this->insert_tweet_count_table('day', $brand_id, $tweet_count_day, $until_date_insert_db_format, false, $next_results);
            break;
          }
          $this->insert_tweet_count_table('day', $brand_id, $tweet_count_day, $until_date_insert_db_format);
          
        // 時間のみ
        }else{
          // API制限で中断された場合
          if($limit_flg) {
            Log::debug('この通貨は検索が中断されました。next_resultsを記録しておき、breakします。');
            Log::debug($params['next_results']);
            $this->insert_tweet_count_table('hour', $brand_id, $tweet_count, $until_date_insert_db_format, false, $next_results);
            break;
          }else{
            Log::debug('コンプリートしていますので完了時刻を挿入します。');
            Log::debug('8--$until_date_insert_db_format:'.$until_date_insert_db_format);
            $this->insert_tweet_count_table('hour', $brand_id, $tweet_count, $until_date_insert_db_format);
          }
        }
        Log::debug($search_word.'のツイート検索及びDB登録全て完了しました。次の検索ワードに移ります。');
      }
      
      // ------------------------------------
      // ⑨ その時間のツイート検索が完了したかの確認
      // ------------------------------------
      if($search_type) {
        $complete_check = TweetCountDay::where('complete_flg', false)->first();
      }else{
        $complete_check = TweetCountHour::where('complete_flg', false)->first();
      }
  
      // complete_flgがtrueのレコードが存在している時(検索未完了通貨がある)
      if(isset($complete_check)) {
        Log::debug('ツイート検索の途中で終了しました。次回続きから再開します。');
        Log::debug('中断時brand_id:'.$complete_check->brand_id);
        Log::debug('complete_flg:'.$complete_check->complete_flg);
        Log::debug('next_results:'.$complete_check->next_results);
        $Updated_tweet_count->fill([
            'complete_flg' => false,
            'updated_at' => $until_date_insert_db_format
        ])->save(['timestamps' => false]);
      }else{
        // 全ての検索が完了している場合、complete_flgをtrueにする。
        Log::debug('全件検索完了・complete_flgがtrueのテーブルも存在しません。updated_at_tablesを更新します。');
        $until_date_insert_db_format->format('Y-m-d H:i:s');
        $Updated_tweet_count->fill([
            'complete_flg' => true,
            'updated_at' => $until_date_insert_db_format
        ])->save(['timestamps' => false]);
      }
      Log::debug('===========================================================');
      Log::debug('▲▲▲▲▲▲▲▲▲▲▲▲▲▲ 通貨ツイート検索を終了します。 bye ▲▲▲▲▲▲▲▲▲▲▲▲▲▲');
      Log::debug('===========================================================');
    }
    
    // =======================================
    // ツイート数を指定したテーブルに新規登録する
    // =======================================
    public function insert_tweet_count_table(string $table_type, $brand_id, $tweet_count, $updated, $complete_flg = true, $next_results = null) {
      Log::debug('=================================================================');
      Log::debug('TwitterController.insert_tweet_count_table 指定のDBにツイート数を登録');
      Log::debug('=================================================================');
      // 拡張を考え、過去の集計データも残しておく
      Log::debug('$updated: '.$updated);
      
      // $table_typeに応じたtweet_countsテーブルを取得する
      // $table_type... 0:hour 1:days
      Log::debug($table_type.'のtweet_countテーブルのモデルを取得します。');
      switch ($table_type){
        case 'hour':
          Log::debug('tweet_count_hoursテーブルインスタンスを取得します。complete_flがfalsのものがあれば代わりにそちらを取得します。');
          // $model = new TweetCountHour();
          $model = TweetCountHour::firstOrNew(['complete_flg' => false]);
          break;
        case 'day':
          Log::debug('tweet_count_daysテーブルインスタンスを取得します。complete_flがfalsのものがあれば代わりにそちらを取得します。');
          Log::debug('$updated:'.$updated);
          // $model = new TweetCountDay();
          $model = TweetCountDay::firstOrNew(['complete_flg' => false]);
          break;
        case 'week':
          Log::debug('tweet_count_weeksテーブルインスタンスを取得します。complete_flgがfalseのものがあれば代わりにそちらを取得します。');
          // $model = new TweetCountWeek();
          $model = TweetCountWeek::firstOrNew(['complete_flg' => false]);
          break;
      }
      
      // 新しくデータを挿入する
      Log::debug('取得したモデルに新しくレコードを追加or更新します。');
      Log::debug('$updated:'.$updated);
  
      $model->fill([
          'brand_id' => $brand_id,
          'tweet_count' => $tweet_count,
          'complete_flg' => $complete_flg,
          'next_results' => $next_results,
          'updated_at' => $updated,
      ])->save(['timestamps' => false,]);
      
    }
  
    
    // =======================================
    // バッチ用・(days)n日前のツイートをカウントし取得する
    // =======================================
    public function start_tweet_count_days(){
      Log::debug('====================================');
      Log::debug('start_tweet_count_days:バッチ:日付別ツイート検索');
      Log::debug('====================================');
      $SEARCH_TYPE = 1;
      // 1,Brandsモデルのレコード数を取得する
      $brands_count = Brand::all()->count();
      
      // 2,今日の日付を取得
      $today = CarbonImmutable::today(); //2020-10-10 00:00:00

      // for文で7日前から回していく
      for ($i = 7; $i > 0;$i--){
        Log::debug($i.'日前のツイート検索が完了しているか確かめます。');
        $checkday = $today->subDays($i - 1);
        $checkday_format = $checkday->format('Y-m-d');
        
        Log::debug('$checkday:'.$checkday.' $checkday_format:'.$checkday_format);
        
        // 指定の検索時刻の日付データが、通貨の数だけ取得完了できているか確認
        $day_count = TweetCountDay::where('updated_at', 'LIKE', "$checkday_format%")
            ->where('complete_flg', true)
            ->get()
            ->count();
        
        Log::debug('$day_count:'.$day_count);
        
        // brandsの数だけcomplete_flgがtrueのレコードが取れていれば、その日の集計が完了している。
        if(isset($day_count) && $day_count == $brands_count){
          Log::debug($checkday_format.'の検索は全て完了しています。');
          Log::debug('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
          continue;
        }else{
          Log::debug('未実施・または完了していないようです。検索を開始します。');
          $this->count_tweets($SEARCH_TYPE, $i);
          Log::debug('一度検索を行ったので今回はここで終了となります。');
          break;
        }
      }
      Log::debug('============================');
      Log::debug('定期ツイート検索(day)を終了します');
      Log::debug('============================');
    }
    
  
    // ======================================================
    // バッチ用・(weeks)7日分の集計データを合算し、1週間分のデータとして保存する
    // ======================================================
    public function make_tweet_count_week(){
      Log::debug('=======================================');
      Log::debug('定期 TwitterController.make_week_tweet_count');
      Log::debug('=======================================');
      // brandsテーブルのレコード数を取得
      $brands_count = Brand::all()->count();
      // 今日の日付
      $today = CarbonImmutable::today();
      $today_db_format = $today->format('Y-m-d H:i:s');
      $today_search_format = $today->format('Y-m-d');
      
      // 時間テーブルを確認し、本日の処理が完了していたらそのまま終了する
      $updated_at_table_model = new UpdatedAtTable();
      $Updated_tweet_count = $updated_at_table_model
          ->where('id', 4)
          ->where('updated_at', 'LIKE', "$today_search_format%")
          ->first();
      
      // 既にその時間のレコードが存在する場合、complete_flgをチェックし、その時間の更新が完了しているかを確認する
      if(isset($Updated_tweet_count)) {
        // 完了している場合
        if ($Updated_tweet_count->complete_flg) {
          Log::debug('この時間の集計は完了しています。');
          Log::debug('▲▲▲▲▲▲▲ 終了します。 bye ▲▲▲▲▲▲▲');
          Log::debug('===================================');
          exit();
        }
        Log::debug('レコードは存在しますが、complete_flgはfalseです。以降の処理に続きます。');
      }
      
      // tweet_count_daysテーブルのそれぞれのIDごとに、新着上位7件を取得する。
      Log::debug('日付テーブルの上位７件を取得します。');
      for ($i = 0;$i < $brands_count;$i++){
        $count_days = TweetCountDay::where('brand_id', $i + 1)
            ->where('complete_flg', true)
            ->orderBy('id', 'DESC')
            ->take(7)
            ->get();
        
        if($count_days->count() < 7) {
          Log::debug('日付データが7件に到達していません。');
          break;
        }
        
        // 取得した日付データ７件分を回し、ツイート数をカウントする。
        $total = 0;
        foreach ($count_days as $count_day){
          Log::debug($count_day->tweet_count.'件のカウントを追加します。');
          $total += $count_day->tweet_count;
          Log::debug('現在カウント総数:'.$total);
        }
        
        // カウント数の総計をDBに登録する
        Log::debug('カウント総数をDB登録します');
        $this->insert_tweet_count_table('week', $i + 1, $total, $today_db_format);
      }
      $updated = $updated_at_table_model->find(4);
      $updated->fill([
          'complete_flg' => true,
          'updated_at' => $today_db_format
      ])->save();
      Log::debug('▲▲▲▲▲▲▲ 終了します。 bye ▲▲▲▲▲▲▲');
      Log::debug('===================================');
    }
    
  
    // =======================================
    // 認証ユーザーによるコネクションインスタンスの作成
    // =======================================
    // 引数は、ユーザーのアクセストークン と アクセストークンシークレットの2つ
    private function connection_instanse_users($token, $token_secret)
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
    private function connection_instanse_app()
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
    private function connection_instanse_OAuth2()
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
