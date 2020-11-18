<?php

namespace App\Http\Controllers;

use App\Models\CoincheckPrice;
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
