<?php

namespace App\Http\Controllers;

use Coincheck\Coincheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoinCheckController extends Controller
{
    /*
     * CoincheckAPIのtickerで取得可能な通貨は「ビットコイン」のみ
     * TODO 大多数の通貨で24時間以内の取引価格が"不明"になってしまうがそれでも良いか確認する
     */
  
    // ==========================
    // BTCのティッカーを取得する
    // ==========================
    public function get_ticker(){
      // high 24時間での最高取引価格
      // low 24時間での最安取引価格
      // 他にも取得可能な情報はある
      
      // コインチェックインスタンスを作成
      $coincheck = $this->make_coincheck_instanse();
      
      // tickerを取得する
      $response = $coincheck->ticker->all();
      Log::debug('$response :'.print_r($response, true));
      
      return $response;
    }
    
    // ==========================
    // 24時間での取引価格をDBに保存する
    // ==========================
    
  
  
  
  
    
    
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
