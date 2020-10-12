<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleNewsController extends Controller
{
  
  /*
   *
   * $keyword: ニュース検索のキーワード
   * $max_num: 取得記事数の上限
   */
  public function get_news(){
    
    // TODO getキーワードにスクリプト攻撃が混ざる可能性を考察すること
    $keywords = !empty($_GET['keywords']) ? $_GET['keywords'] : '仮想通貨';
    $max_num = 15;
    Log::debug('$keywords: '.$keywords);
    Log::debug('$max_num: '.$max_num);
    
    set_time_limit(90);
    
    // キーワード検索時のベースURL、末尾の"q="に続くように検索キーワードを付与する
    // $API_BASE_URL = "https://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=atom&q=";
    $API_BASE_URL = "https://news.google.com/rss/news?hl=ja&gl=JP&ceid=JP&ie=UTF-8&oe=UTF-8&q=";
    
    // 検索キーワードの文字コードを変更する
    $query = urlencode(mb_convert_encoding($keywords, "UTF-8", "auto"));
    
    Log::debug('$query: '.$query);
    
    // APIへのリクエストURLを作成(検索キーワードを付与)
    $api_url = $API_BASE_URL.$query;
    
    Log::debug('今回の検索URLは: '.$api_url);
    
    
    // APIにアクセスする。その結果はsimplexmlに格納する。
    $content = file_get_contents($api_url);
    $xml = simplexml_load_string($content);
    
    // print_r($xml);
    Log::debug('$xml: '.$xml);
    
    // 記事エントリを取り出す
    $data = $xml->entry;
    
    $list = [];
    $list_gn = [];
    
    // 記事のタイトルとURLを取り出して、配列に格納する
    foreach ($data as $item) {
      $list[$i]['title'] = mb_convert_encoding($item->title ,"UTF-8", "auto");
    
      $description = mb_convert_encoding($item->description , "UTF-8", "auto");
    
      $description=strip_tags($description);
    
      $description= mb_strimwidth ($description,0,$news_letters, "", "UTF-8");
    
      $list[$i]['description'] =$description;
    
      $list[$i]['url'] = $item->link->attributes()->href;
    
      $i++;
    
    }
  
    if(count($list)>$max_num){
      for ($i = 0;
           $i < $max_num;
           $i++){
        $list_gn[$i] = $list{$i};
      
        $i++;
      
      }
    }else{
      $list_gn = $list;
    
    }

//print_r($list_gn);
  
    return $list_gn;
    // for ($i = 0; $i < count($data); $i++) {
    //
    //   $list[$i]['title'] = mb_convert_encoding($data[$i]->title, "UTF-8", "auto");
    //   $url_split = explode("=", (string)$data[$i]->link->attributes()->href);
    //   $list[$i]['url'] = end($url_split);
    //
    //   Log::debug($list[$i]['url']);
    // }
    //
    // // 取得上限($max_num)以上の記事数の場合、切り捨てる
    // if(count($list) > $max_num){
    //   for ($i = 0; $i < $max_num; $i++) {
    //     $list_gn[$i] = $list{$i};
    //     $i++;
    //   }
    // }else{
    //   $list_gn = $list;
    // }
    //
    // // 配列を出力
    // return $list_gn;
    
  }
  
  
}
