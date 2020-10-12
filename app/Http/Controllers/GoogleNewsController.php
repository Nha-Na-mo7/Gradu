<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleNewsController extends Controller
{
  
  /*
   * Googleニュース検索・データ取得関数 atom
   * $keyword: ニュース検索のキーワード
   * $max_num: 取得記事数の上限
   * $news_letters: 表示するニュースの文字数
   */
  public function get_news(){
    
    // TODO getキーワードにスクリプト攻撃が混ざる可能性を考察すること
    // GETパラメータの値を元に、ニュースを取得する
    $keywords = !empty($_GET['keywords']) ? $_GET['keywords'] : '仮想通貨';
    $max_num = 10;
    $news_letters = 200;
    Log::debug('$keywords: '.$keywords);
    Log::debug('$max_num: '.$max_num);
    
    // 実行時間。90秒。
    set_time_limit(90);
    
    // キーワード検索時のベースURL、末尾の"q="に続くように検索キーワードを付与する
    // TODO 必要ない$API_BASE_URLはコメントごと削除すること
    // $API_BASE_URL = "https://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=atom&q=";
    // $API_BASE_URL = "https://news.google.com/news?hl=ja&gl=JP&ceid=JP&ie=UTF-8&oe=UTF-8&output=atom&q=";
      $API_BASE_URL = "https://news.google.com/atom/news?hl=ja&gl=JP&ceid=JP&ie=UTF-8&oe=UTF-8&q=";
    
    // 検索キーワードの文字コードを変更する
    $query = urlencode(mb_convert_encoding($keywords, "UTF-8", "auto"));
    
    // APIへのリクエストURLを作成(検索キーワードを付与)
    $api_url = $API_BASE_URL.$query;
    
    Log::debug('今回の検索URLは: '.$api_url);
    
    // APIにアクセスする。その結果はsimplexmlに格納する。
    $content = file_get_contents($api_url);
    $xml = simplexml_load_string($content);
    
    // $xml確認用
    // print_r($xml);
    
    // 記事エントリを取り出す。entry1つに記事情報が詰まっている。
    $data = $xml->entry;
    
    // entry1つ1つから"title"、"updated"とURLを取り出して、配列に格納する
    for ($i = 0; $i < count($data); $i++) {
    
      // エントリーのタイトル
      $list[$i]['title'] = mb_convert_encoding($data[$i]->title, "UTF-8", "auto");
      
      // デスクリプション
      // $description = mb_convert_encoding($data[$i]->description , "UTF-8", "auto");
      //
      // $description=strip_tags($description);
      //
      // $description= mb_strimwidth ($description, 0, $news_letters, "", "UTF-8");
      //
      // $list[$i]['description'] = $description;
      
      // エントリーの更新日
      $list[$i]['updated'] = mb_convert_encoding($data[$i]->updated, "UTF-8", "auto");
      
      // URL、(string)でキャストすることで、SimpleXMLElement Objectの形から普通の配列として表示できる
      $list[$i]['url'] = (string)$data[$i]->link->attributes()->href;
  
    }
    
    // $max_numの数値以上の記事がある場合、オーバーした分の記事を削る
    if(count($list) > $max_num){
      for ($i = 0; $i < $max_num; $i++) {
        $list_gn[$i] = $list{$i};
      }
    }else{
      $list_gn = $list;
    }
    
    // 取得したニュースの配列を返却
    return $list_gn;
  }
  
  
}