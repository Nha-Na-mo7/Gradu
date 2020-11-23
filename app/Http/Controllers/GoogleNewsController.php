<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleNewsController extends Controller
{
  
  /* ====================================================
   * Googleニュース 検索
   * ====================================================
   * $keyword: ニュース検索のキーワード
   * $max_num: 取得記事数の上限。APIの仕様上最大100件
   */
  public function get_news(){
    
    Log::debug('============================================');
    Log::debug('GoogleNewsController/get_news Newsを取得します');
    Log::debug('============================================');
    // ------------------
    // APIアクセス前準備
    // ------------------
    
    // GETパラメータの値を元に、ニュースを取得する
    $keywords = filter_input(INPUT_GET, 'keywords') ? filter_input(INPUT_GET, 'keywords') : '仮想通貨';
    $max_num = 100;
    
    Log::debug('検索ワード: '.$keywords);
    
    // 最大実行時間・90秒。
    set_time_limit(90);
    
    // キーワード検索時のベースとなるURL
    // 文字化け防止のためにUTF-8を指定
    $API_BASE_URL = 'https://news.google.com/rss/search?ie=UTF-8&oe=UTF-8&hl=ja&gl=JP&q=';
    // 日本語のニュースに限定する
    $API_PARAM_URL = '&ceid=JP:ja';
    
    // 検索キーワードの文字コードを変更
    $query = urlencode(mb_convert_encoding($keywords, "UTF-8", "auto"));
    
    // APIへのリクエストURLを作成
    $api_url = $API_BASE_URL . $query . $API_PARAM_URL;
    Log::debug('APIリクエストURL :'.$api_url);
    
    
    
    // --------------------
    // APIへリクエストを飛ばす
    // --------------------
    // APIにリクエスト。その結果はsimplexmlに格納する。
    $content = file_get_contents($api_url);
    $xml = simplexml_load_string($content);
    
    // $xml確認用
    // Log::debug(print_r($xml, true));
    
    
    
    // ----------------------------------------
    // アクセス後、必要な情報をレスポンスするまでの工程
    // ----------------------------------------
    // 記事エントリを取り出す。
    $data = $xml->channel->item;
    
    // 記事数をカウントして、0だった場合は空のままレスポンスを返す
    if(count($data)) {
      Log::debug('見つけた記事数: '. count($data));
    } else {
      Log::debug('記事は0件でした。');
      return $data;
    }
    
    // "title"、"pubDate"、"source"、URLを取り出して、配列に格納する
    for ($i = 0; $i < count($data); $i++) {
      // エントリーのタイトル
      $entry_list[$i]['title'] = mb_convert_encoding($data[$i]->title, "UTF-8", "auto");

      // エントリーの更新日(UNIXタイムスタンプで取得)
      $entry_list[$i]['pubDate'] = strtotime($data[$i]->pubDate);
      
      // 記事の発行元
      $entry_list[$i]['source'] = mb_convert_encoding($data[$i]->source, "UTF-8", "auto");
      
      // URL
      $entry_list[$i]['url'] = mb_convert_encoding($data[$i]->link, "UTF-8", "auto");
    }
  
    // 記事を新着順に並べ替える。
    // GoogleニュースAPIが取得する順番は必ずしも新着順ではない
    // また、サービスの特性上トレンドを追うことが目的である為、新着ニュースを知ることが最適。
    foreach ($entry_list as $key => $value) {
      $pubDate[$key] = $value['pubDate'];
    }
    array_multisort($pubDate, SORT_DESC, $entry_list);
  
    // $max_numの数値以上の記事がある場合、オーバーした分の記事を削る
    if(count($entry_list) > $max_num){
      for ($i = 0; $i < $max_num; $i++) {
        $scraped_entry_list[$i] = $entry_list[$i];
      }
    }else{
      $scraped_entry_list = $entry_list;
    }
    
    // レスポンスする記事の配列
    // Log::debug('レスポンスする記事の配列: '. print_r($scraped_entry_list, true));
    
    // 取得したニュースの配列を返却
    return $scraped_entry_list;
  }
}
