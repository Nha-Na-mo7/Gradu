<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\TweetCountDay;
use App\Models\TweetCountHour;
use App\Models\TweetCountWeek;
use App\Models\UpdatedAtTable;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrendTweetController extends Controller
{
    // search/tweetsAPIが15分間の間に検索できる最大数。誤差でAPI制限に
    const SEARCH_TWEETS_LIMIT = 440;
    
    // updated_at_tablesにおけるtwitter_accountsを参照するテーブルのID
    const UPDATED_AT_TABLES__HOURS_ID = 2;
    const UPDATED_AT_TABLES__DAYS_ID = 3;
    const UPDATED_AT_TABLES__WEEKS_ID = 4;
    
    
    
    // ==============================================
    // 仮想通貨や通貨ごとのツイート数を集計する
    // ==============================================
    /* 処理の流れ
     * ⓪ 現在の日付でレコードがあるのかを呼び出す
     * ① brandsテーブル(取り扱う通貨名)のレコードを全て取得し、銘柄名とカタカナ名を組み合わせた検索ワードを作り、配列に格納
     * ＞(例:'BTC OR ビットコイン')
     * ② インスタンスを作成し、検索ワード配列を回す
     *
     * ③ 検索パラメータを設定
     * ④ その検索ワードを全件検索し終える、またはAPI制限上限に到達するまでループしながら検索をかける
     * ⑤ 取得できたツイートの総数をカウントする
     * ⑥ 1度の検索で全件取得しきれなかった場合、レスポンスに付与されたnext_resultsのURLを使って再度検索を行う
     *
     * ④〜⑥を繰り返し、期間内の取得ツイートが無くなったらfor文ループを抜ける
     *
     * ⑦ 検索で取得できた通貨のツイート数をDBに登録する
     * ⑧ その時間のツイート検索が完了したかの確認をして、updated_at_tablesに更新時刻、complete_flgを記述し終了。
     *
     * 途中でAPI制限にかかった場合、15分の待機をした上で同条件でもう一度検索を開始する。
     * アプリケーション認証では、15分間の間に450回のツイート検索ができる。
     */
    public function count_tweets($search_type, $sub_times = 1) {
      Log::debug('==============================================');
      Log::debug('TwitterController.count_tweets 銘柄別ツイート数取得');
      Log::debug('==============================================');
      
      /*
       * 1時間以内のツイート全て・24時間以内のツイート全てを集計する。
       * 1週間以内のツイートに関しては過去1日以内のツイート×7日分の合計値で算出する
       * ・APIの仕様で、7日間の期間指定をしたとしても取得できなくなる場合がある
       * ・毎時毎時7日分のツイート検索を行うのはサービスとしても負荷が大きい
       * ・一回でも7日分のツイート数さえ取得してしまえば、後は1日以内のツイートを算出すればよくなり負荷が軽くなる。余計なAPI制限にかかってエラーを起こす危険も減少する。
       */
      
      
      // searchType 0:1時間以内のみ 1:過去1日間に検索をかける
      
      // 検索再開フラグ
      $resume_flg = false;
      // API制限などで検索が続行できなくなった場合のフラグ
      $limit_flg = false;
      // リクエスト回数カウンター
      $request_count = 0;
      // 通貨ID
      $brand_id = 0;
      // since(この時間以降のツイートに絞って検索をかける)
      $since_date = '';
      // until(検索を開始した時点での現在時刻。)
      $until_date = '';
      // 次ページ用のパラメータ
      $next_results = '';
      // 検索で取得できた通貨ごとのツイートの総数
      $tweet_count = 0;
      
      // updated_at_tablesのモデルに渡す引数
      $table_id = ($search_type == 0) ? self::UPDATED_AT_TABLES__HOURS_ID : self::UPDATED_AT_TABLES__DAYS_ID;
      
      
      // --------------------------------------------------
      // ⓪ 現在の日付時刻でレコードがあるかを呼び出す
      // --------------------------------------------------
      // 現在時刻
      $now = CarbonImmutable::now();
      
      // 検索開始時刻に既に更新が行われているかを確認する
      switch ($search_type) {
        case 0:
          $search_time = $now->format('Y-m-d H:');
          break;
        case 1:
          // テーブル検索用、その日の集計が完了しているかの判定なので検索は日付だけでOK
          $sub_now = $now->subDays($sub_times - 1);
          $search_time = $sub_now->format('Y-m-d');
          break;
      }
      
      // テーブルを確認する。%を付与してLIKE検索をする。
      Log::debug('$search_time:'.$search_time);
      $Updated_tweet_count = UpdatedAtTable::where('id', $table_id)
          ->where('updated_at', 'LIKE', "$search_time%")
          ->first();
      
      
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
          
          // 検索終了後にDBに格納する更新時刻を元々入っていたレコードから取得しておく
          $until_date_insert_db_format = new CarbonImmutable($Updated_tweet_count->updated_at);
        }
        
      }else{
        Log::debug('集計が未実施です。');
        $Updated_tweet_count = UpdatedAtTable::where('id', $table_id)->first();
        
        // その時間のcomplete_flgをfalseに変更
        $Updated_tweet_count->fill([
            'complete_flg' => false,
        ])->save();
        
      }
      
      
      // 引数の検索条件によって、sinceを分ける(0: -1hour、1: -1day)
      // さらに、対応するTweetCountモデルを取得する
      Log::debug('遡って取得する時間(since)を算出します。');
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
            $resume_brand_id = $tweetCountModel->brand_id;
            // 途中までのカウントを取得する
            $tweet_count = $tweetCountModel->tweet_count;
            // type:0なので1時間前の時刻を取得する
            $since_date = (new CarbonImmutable($Updated_tweet_count->updated_at))->subHour();
            // 検索再開地点のnext_resultsパラメータを取得する
            $next_results = $tweetCountModel->next_results;
            
          }else{
            $until_date_insert_db_format = $now;
          }
          break;
        
        case 1:
          Log::debug('$search_type :'.$search_type.'(= day)です。前日の00:00~00:00までの時刻を取得します。');
          // n日前の時刻を取得する 例えば$sub_timesが2なら...
          $today = CarbonImmutable::today(); // 2020-10-03 00:00:00
          $until_date = $today->subDays($sub_times - 1);//2020-10-02 00:00:00
          $since_date = $today->subDays($sub_times); // 2020-10-01 00:00:00
          
          // 検索用・DB用にフォーマットを修正
          $until_date = $until_date->format('Y-m-d_H:i:s_\J\S\T'); //2020-10-02_00:00:00_JST
          $since_date = $since_date->format('Y-m-d_H:i:s_\J\S\T'); //2020-10-01_00:00:00_JST
          
          // 続きからの場合
          if($resume_flg) {
            $tweetCountModel_day = TweetCountDay::where('complete_flg', false)->first();
            
            // 更新途中の銘柄IDを取得する
            $resume_brand_id = $tweetCountModel_day->brand_id;
            // 途中までのカウントを取得する
            $tweet_count = $tweetCountModel_day->tweet_count;
            // 次のパラメータを取得する
            $next_results = $tweetCountModel_day->next_results;
            
            
          }else{
            $until_date_insert_db_format = $today->subDays($sub_times - 1); //2020-10-02 00:00:00
          }
          break;
      }
      
      
      // ----------------------------------------------------------------------------------------------------
      // ① brandsテーブル(取り扱う通貨名)のレコードを全て取得し、銘柄名とカタカナ名を組み合わせた検索ワードを作り、配列に格納
      // ----------------------------------------------------------------------------------------------------
      $search_words = $this->make_brands_searchwords_array();
      if($search_words === false){
        exit();
      }
      
      // ------------------------------------
      // ② インスタンスを作成し、検索ワード配列を回す
      // ------------------------------------
      // アプリケーション認証インスタンス作成
      $connection = (new TwitterController)->connection_instanse_OAuth2();
      
      // 検索ワード配列回し開始
      foreach ($search_words as $search_word) {
        // --------------------------------------
        // ③ パラメータを設定する
        // --------------------------------------
        // インクリメントしてbrandsテーブルのidと対応
        $brand_id++;
        
        // 対応する検索ワードが続きからでない場合はツイートカウントを初期化する
        if(!$resume_flg) {
          $tweet_count = 0;
        }
        
        // 現在の検索中の通貨IDと、途中まで実施済みの通貨IDを比較
        if($resume_flg){
          Log::debug('再開させる通貨ID:'.$resume_brand_id.' 現在の検索中通貨ID:'.$brand_id);
          if($resume_brand_id > $brand_id) {
            Log::debug('既に検索終了済みの通貨です。次の通貨の検索に移行します。');
            continue;
          }else{
            Log::debug('この通貨の検索を再開します。$tweet_count :'.$tweet_count);
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
        
        // Log::debug('API用の検索パラメータを設定しました: '.print_r($params, true));
        
        
        // ---------------------------------------------------
        // ④ その検索ワードの全件検索し終える、
        //    またはAPI制限上限に到達するまでループしながら検索をかける
        // ---------------------------------------------------
        for (;$request_count < self::SEARCH_TWEETS_LIMIT;) {
          
          // APIにリクエストを飛ばす
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
            
            // API制限の場合(エラーコード88)は知らせる
            if($result_tweets['errors'][0]['code'] === 88) {
              Log::debug('code = 88、API制限です。');
            }
            
            // 検索が完了しなかった場合のフラグを立てる
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
          $tweet_count += count($result_tweets['statuses']);
          Log::debug($params['q'].'の結果、'.count($result_tweets['statuses']).'件のツイートが取得できました。');
          
          
          // -------------------------------------------------------------
          // ⑥ 1度の検索で全件取得しきれなかった場合、
          // search_metadataのnext_resultにURLが指定されるのでこれをparamsに加えもう一度検索する
          // 指定期間内の取得ツイートが無くなったら、for文ループを抜ける。
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
          parse_str($next_results, $params);
          Log::debug('$paramsに$next_resultsの情報を追加しました。ループの先頭に戻ります。');
          
          // 再開フラグはfalseにする
          $resume_flg = false;
        }
        
        
        // -------------------------------------
        // ⑦ 検索した通貨のツイート数をDB登録する
        // -------------------------------------
        Log::debug('for文ループが終了しました。取得した'.$search_word.'のツイート総数をDBに登録します。');
        Log::debug($search_word.'の取得ツイート総数は'.$tweet_count);
        
        // hourかdayかによって、登録するテーブルが変わる
        switch ($search_type){
          case 0:
            $count_table_name = 'hour';
            break;
          case 1 :
            $count_table_name = 'day';
            break;
        }
        
        // limitフラグが立っていれば、next_resultsの登録が済み次第終了する
        if($limit_flg) {
          Log::debug('この通貨は検索が中断されました。next_resultsを記録しておき、breakします。');
          $this->insert_tweet_count_table($count_table_name, $brand_id, $tweet_count, $until_date_insert_db_format, false, $next_results);
          break;
        }else{
          Log::debug('コンプリートしていますので完了時刻を挿入します。');
          $this->insert_tweet_count_table($count_table_name, $brand_id, $tweet_count, $until_date_insert_db_format);
        }
        Log::debug($search_word.'のツイート検索及びDB登録全て完了しました。次の検索ワードに移ります。');
      }
      
      // ------------------------------------
      // ⑧ その時間のツイート検索が完了したかの確認
      // ------------------------------------
      switch ($search_type){
        case 0:
          $complete_check = TweetCountHour::where('complete_flg', false)->first();
          break;
        case 1 :
          $complete_check = TweetCountDay::where('complete_flg', false)->first();
          break;
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
        ])->save();
        
      }else{
        // 全ての検索が完了している場合、complete_flgをtrueにする。
        Log::debug('全件検索完了・complete_flgがtrueのテーブルも存在しません。updated_at_tablesを更新します。');
        $Updated_tweet_count->fill([
            'complete_flg' => true,
            'updated_at' => $until_date_insert_db_format
        ])->save();
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
      
      // $table_typeに応じたtweet_countsテーブルを取得する
      // $table_type... 0:hour 1:days 2:weeks
      switch ($table_type){
        case 'hour':
          Log::debug('tweet_count_hoursテーブルインスタンスを取得します。complete_flがfalsのものがあれば代わりにそちらを取得します。');
          $model = TweetCountHour::firstOrNew(['complete_flg' => false]);
          break;
        case 'day':
          Log::debug('tweet_count_daysテーブルインスタンスを取得します。complete_flがfalsのものがあれば代わりにそちらを取得します。');
          Log::debug('$updated:'.$updated);
          $model = TweetCountDay::firstOrNew(['complete_flg' => false]);
          break;
        case 'week':
          Log::debug('tweet_count_weeksテーブルインスタンスを取得します。complete_flgがfalseのものがあれば代わりにそちらを取得します。');
          $model = TweetCountWeek::firstOrNew(['complete_flg' => false]);
          break;
      }
      
      // 新しくデータを挿入する
      Log::debug('取得したモデルに新しくレコードを追加or更新します。');
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
          Log::debug('一度検索を行ったので一旦定期ツイート検索(day)は終了となります。');
          Log::debug('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
          break;
        }
      }
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
            ->latest('id')
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
    // brandsテーブルの全通貨名を配列にして返却する
    // =======================================
    public function make_brands_searchwords_array(){
      Log::debug('=============================');
      Log::debug('make_brands_searchwords_array');
      Log::debug('=============================');
      Log::debug('brandsテーブルから全ての通貨レコードを取得します。');
      $brandsController = new BrandController();
      $all_brands = $brandsController->get_brands();
      
      // 万が一brandsテーブルが空だったとき(普通この処理に辿り着く事は無い)
      if ($all_brands->isEmpty()) {
        Log::debug('検索すべき通貨名が取得数0でした。seederを打ったか確認してください。');
        return false;
      }
      
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
      return $search_words;
    }
}
