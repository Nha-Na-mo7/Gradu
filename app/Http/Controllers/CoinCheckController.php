<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CoincheckPrice;
use App\Models\TweetCountDay;
use App\Models\TweetCountHour;
use App\Models\TweetCountWeek;
use App\Models\UpdatedAtTable;
use Carbon\CarbonImmutable;
use Coincheck\Coincheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoinCheckController extends Controller
{
    /*
     * CoincheckAPIのtickerで取得可能な通貨は「ビットコイン」のみ
     */
  
    public function __construct()
    {
      $this->middleware('auth');
    }
    // brandsテーブルのBTCの通貨ID
    const BTC_ID = 1;
    
    // =====================================================
    // ビューを返却する
    // =====================================================
    public function index(){
      return view('pages.trends');
    }
    
    // =====================================================
    // DBから24時間以内の最高・最安取引価格情報を取得し返却する
    // =====================================================
    public function get_transaction_price($brand_id = null){
      Log::debug('CoinCheckController.get_transaction_price 24時間以内の取引価格情報');
  
      if($brand_id != null) {
        $result = CoincheckPrice::where('brand_id', $brand_id)->latest('id')->first();
  
        // 取得できない(データがない)通貨の時は空文字で返却する
        if(isset($result)){
          return $result;
        }else{
          Log::debug($brand_id.'の通貨は取引価格情報がありませんでした。');
          return '';
        }
      }
      
      Log::debug('全部取得します');
      // 価格情報を格納する配列
      $all_transaction = [];
  
      // brandsテーブルのレコード数
      $brands_count = Brand::all()->count();
      
      // テーブルから通貨ごとの24h取引価格を取得する。
      for ($i = 0; $i < $brands_count ; $i++){
        $result = CoincheckPrice::where('brand_id', ($i + 1))
            ->latest('id')
            ->first();
        
        if(isset($result)){
          $all_transaction[$i] = $result;
        }else{
          $all_transaction[$i] = '';
        }
      }
      return $all_transaction;
    }

    // =====================================================
    // DBから、過去1時間or過去1日or1週間の各通貨のツイート数を取得する
    // =====================================================
    public function get_tweet_count(){
      Log::debug('CoinCheckController.get_tweet_count ツイート数を取得する');
      // 参照するテーブルが違うだけで殆ど同じ処理をするため、引数で分ける
      // $type: 0...hour 1...day 2...week
      $type = filter_input(INPUT_GET, 'type');
      
      // 現在時刻で得られる最新の時刻で検索し、コンプリートフラグが立っているかを確認する
      $now = CarbonImmutable::now();
  
      switch ($type){
        case 0:
          Log::debug('過去1時間のツイート数を取得します。');
          $table_id = 2;
          $search_time = $now->format('Y-m-d H:');
          break;
        case 1:
          Log::debug('過去1日のツイート数を取得します。');
          $table_id = 3;
          $search_time = $now->format('Y-m-d');
          break;
        case 2:
          Log::debug('過去1週間のツイート数を取得します。');
          $table_id = 4;
          $search_time = $now->format('Y-m-d');
          break;
      }
  
      // 現在時刻を元に集計完了しているかcomplete_flgを確認する。
      $Updated_tweet_count = UpdatedAtTable::where('id', $table_id)
          ->where('updated_at', 'LIKE', "$search_time%")
          ->where('complete_flg', true)
          ->first();
      
      // その時間に集計完了している場合、レコードが存在するので取得し返却する
      if(isset($Updated_tweet_count)) {
        switch ($type){
          case 0:
            $result = TweetCountHour::with(['brand'])->where('updated_at', 'LIKE', "$search_time%")->get();
            break;
          case 1:
            $result = TweetCountDay::with(['brand'])->where('updated_at', 'LIKE', "$search_time%")->get();
            break;
          case 2:
            $result = TweetCountWeek::with(['brand'])->where('updated_at', 'LIKE', "$search_time%")->get();
            break;
        }
        return $result;
        
      // 集計が完了していない場合、1つ前の集計時刻に遡り取得する
      }else{
        switch ($type){
          case 0:
            $old_search_time = $now->subHour()->format('Y-m-d H:');
            
            $result = TweetCountHour::with(['brand'])->where('updated_at', 'LIKE', "$old_search_time%")->get();
            break;
          case 1:
            $old_search_time = $now->subDay()->format('Y-m-d');
            $result = TweetCountDay::with(['brand'])->where('updated_at', 'LIKE', "$old_search_time%")->get();
            break;
          case 2:
            $old_search_time = $now->subDay()->format('Y-m-d');
            Log::debug('old case2:'.$old_search_time);
            $result = TweetCountWeek::with(['brand'])->where('updated_at', 'LIKE', "$old_search_time%")->get();
            break;
        }

        return $result;
      }
    }
  
  
    // ============================================================
    // バッチ用・24時間に1度、24時間最高or最安取引価格を取得しDB保存する
    // ============================================================
    public function daily_price_check(){
      Log::debug('=======================================');
      Log::debug('定期 CoinCheckController.daily_price_check');
      Log::debug('=======================================');
      
      
      // BTCのtickerを取得する
      Log::debug('---- BTCの取引価格を取得し保存します。 ----');
      $btc = $this->get_btc_ticker();
      
      // 最低取引価格
      $price_min = $btc['low'];
      // 最高取引価格
      $price_max = $btc['high'];
      
      Log::debug('最低取引価格: '.$price_min.' 最高取引価格:'.$price_max);
      $this->insert_price_to_db(self::BTC_ID, $price_min, $price_max);
      Log::debug('---- BTCの処理が終了しました。 ----');
  
  
      Log::debug('====================================');
      Log::debug('▲▲▲▲▲▲▲  処理が全て完了しました。 ▲▲▲▲▲▲▲');
      Log::debug('====================================');
    }
  
  
    // ==========================
    // BTCのティッカーを取得する
    // ==========================
    private function get_btc_ticker(){
      // high 24時間での最高取引価格
      // low 24時間での最安取引価格
      
      // コインチェックインスタンスを作成
      $coincheck = $this->make_coincheck_instanse();
      
      // tickerを取得する
      Log::debug('CoinCheckのAPIを使ってBTCのティッカーを取得します。');
      $response = $coincheck->ticker->all();
      Log::debug('$response :'.print_r($response, true));
      
      return $response;
    }
    
    // ==========================
    // 24時間での取引価格をDBに保存する
    // ==========================
    private function insert_price_to_db($brand_id, $price_min, $price_max){
      Log::debug('DBに24時間の最高or最低取引価格を保存します。');
      Log::debug('$brand_id/$min/max'. $brand_id.' '.$price_min.' '.$price_max);
      // モデルを作成しinsertする
      $coinchech_price = new CoincheckPrice();
      
      $coinchech_price->fill([
          'brand_id' => $brand_id,
          'price_min' => $price_min,
          'price_max' => $price_max
      ])->save();
    }
    
    // =======================================
    // N日前以前の最高最安取引価格のデータを全て削除する
    // =======================================
    // 2週間(14日前)を限度にデータを全て削除する(DBの容量削減のため)
    // $days = N日前かを指定する
    public function delete_price_by_db(int $days) {
      // 今日の日付
      $today = CarbonImmutable::today();
      // N日前の日付
      $subday = $today->subDays($days);
      // フォーマット
      $subday_format = $subday->format('Y-m-d H:i:s');
      // モデルを取得
      $coincheck_price_model = new CoincheckPrice();
      // 指定日以前の全てのデータを削除
      $coincheck_price_model->where('updated_at', '<', $subday_format)->delete();
    }
    
    // ==========================
    // Coincheckインスタンスの作成
    // ==========================
    private function make_coincheck_instanse()
    {
      $access_key = config('services.coincheck')['access_key'];
      $secret_key = config('services.coincheck')['secret_key'];
  
      return new Coincheck($access_key, $secret_key);
    }
}
