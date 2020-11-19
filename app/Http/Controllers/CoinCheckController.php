<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CoincheckPrice;
use App\Models\TweetCountHour;
use App\Models\UpdatedAtTable;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Coincheck\Coincheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoinCheckController extends Controller
{
    /*
     * CoincheckAPIのtickerで取得可能な通貨は「ビットコイン」のみ
     * TODO 大多数の通貨で24時間以内の取引価格が"不明"になってしまうがそれでも良いか確認する
     */
  
    // brandsテーブルの通貨ID
    const BTC_ID = 1;
  

    // =====================================================
    // DBから、過去1時間or過去1日or1週間の各通貨のツイート数を取得する
    // =====================================================
    public function get_tweet_count($type){
      // 参照するテーブルが違うだけで殆ど同じ処理をするため、引数で分ける
      // $type: 0...hour 1...day 2...week
  
      // 現在時刻で得られる最新の時刻で検索し、コンプリートフラグが立っているかを確認する
      $now = CarbonImmutable::now();
  
      switch ($type){
        case 0:
          $table_id = 2;
          $search_time = $now->format('Y-m-d H:');
          break;
        case 1:
          $table_id = 3;
          $search_time = $now->format('Y-m-d');
          break;
        case 2:
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
  
        $result = TweetCountHour::where('updated_at', 'LIKE', "$search_time%")->all();
  
        return $result;
      // 集計が完了していない場合、既に集計済みの最新のものを、ブランドテーブルの数だけ取得する
      }else{
        // Brandsモデルのレコード数
        $brands_count = Brand::all()->count();
        $result = TweetCountHour::where('complete_flg', true)
            ->where('updated_at', 'NOT', "$search_time%")
            ->latest('id')
            ->take($brands_count)
            ->get();
        
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
    
    // ==========================
    // Coincheckインスタンスの作成
    // ==========================
    private function make_coincheck_instanse()
    {
      $access_key = config('services.coincheck')['access_key'];
      $secret_key = config('services.coincheck')['secret_key'];
      
      $coincheck = new Coincheck($access_key, $secret_key);
      
      return $coincheck;
  }
}
