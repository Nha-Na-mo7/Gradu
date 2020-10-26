<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwitterController extends Controller
{
    // 該当のTwitterアカウントを取得する
    public function get_accounts() {
  
      // TODO getキーワードにスクリプト攻撃が混ざる可能性を考察すること
      // GETパラメータの値を元に、Twitterアカウントを取得する
      // 「仮想通貨」がプロフィール、またはアカウント名に含まれていること
      $keywords = !empty($_GET['keywords']) ? $_GET['keywords'] : '仮想通貨';
      // $max_num = 100;
  
      // 実行時間。90秒。
      set_time_limit(90);
  
  
      
      
    }
}
