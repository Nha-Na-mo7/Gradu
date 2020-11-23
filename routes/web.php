<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// =========================
// 認証系(認証なしで利用可能)
// =========================
// トップページ・index.blade.php
Route::get('/', 'IndexController@index');
// 認証系ルート
Auth::routes();
// ログアウト
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');


// ================
// Twitter認証用
// ================
// ①login/twitterのURLをユーザーが踏む
// ↓
// ②Twitterアプリ側が認証をする
// 認証が成功したら...
// ↓
// ③login/twitter/callbackへユーザー情報を返す

// Twitter経由での認証を開始するURL
Route::get('/twitter/auth/begin', 'TwitterController@redirectToTwitterProvider')->name('twitter.begin');
// Twitterアプリケーション側から情報が返ってくるコールバックURL
Route::get('/twitter/auth/callback', 'TwitterController@handleTwitterProviderCallback');


// ================
// other
// ================
// APIのURL以外のあらゆるリクエストに対してはindex.bladeテンプレートを返す
// 画面遷移はフロントエンドのVueRouterが制御する
// Route::get('/{any?}', 'IndexController@index')->where('any', '.+');


// ===========================================
// 各種ページ これより以下は会員ページ・認証必須となる
// ===========================================
Route::group(['middleware' => 'auth'], function (){
  
  // =============================================
  // 銘柄関連
  // =============================================
  // 通貨カラムを全て取得する
  Route::get('/brand', 'BrandController@get_brands')->name('get_brands');
  // 指定の通貨のカラムを取得する
  Route::get('/brand/{brand_id}', 'BrandController@get_brands')->name('get_brands.brandid');
  
  // =============================================
  // Twitter関連
  // =============================================
  // 一覧画面のビュー返却
  Route::get('/accounts', 'TwitterAccountListController@index')->name('accounts.index');
  
  // アカウント一覧画面/テーブルからアカウント情報を取得
  // TODO ルートが間際らしいので変更すること
  Route::get('/accounts/index', 'TwitterAccountListController@accounts_index')->name('accounts.index');
  // アカウントコンポーネント/指定したユーザーの新着ツイートを取得
  Route::get('/accounts/tweet/{tweet_id}', 'TwitterAccountListController@accounts_tweet')->name('accounts.tweet');
  
  
  // 認証ユーザーの自動フォローフラグを切り替える
  Route::post('/accounts/autofollowflg', 'TwitterAccountListController@toggle_auto_follow_flg')->name('accounts.follow');
  // 指定したTwitterAccountListアカウントをフォローする
  Route::post('/accounts/follow', 'TwitterAccountListController@accounts_follow')->name('accounts.follow');
  // 指定したTwitterアカウントのフォローを外す
  // Route::get('/accounts/get', 'TwitterAccountListController@')->name('');
  
  // Twitterとの連携を解除する
  Route::post('/accounts/un_linkage', 'TwitterController@un_linkage')->name('accounts.un_linkage');
  
  // =============================================
  // GoogleNews関連
  // =============================================
  // ビューを返却
  Route::get('/news', 'GoogleNewsController@index')->name('news.index');
  
  // 指定したワードでGoogleNewsAPIを使用し、ニュースを取得する
  Route::get('/news/get', 'GoogleNewsController@get_news')->name('news.get_news');
  
  
  // =============================================
  // トレンド一覧表示関連
  // =============================================
  // トレンド画面のビューを返却
  Route::get('/trends', 'CoinCheckController@index')->name('coincheck.index');
  
  // 過去1時間or1日or1週間のツイート数を取得する
  Route::get('/tweet/count', 'CoinCheckController@get_tweet_count');
  // 指定の通貨の24時間以内での最高・最安取引価格情報を取得する
  Route::get('/transaction/price', 'CoinCheckController@get_transaction_price');
  
  // =============================================
  // ユーザー関連
  // =============================================
  // マイページ・アカウント設定画面のビューを返却する
  Route::get('/mypage', 'MypageController@index')->name('mypage.index');
  
  // ユーザーネームを更新する
  Route::post('/user/update/name', 'UserController@update_name')->name('user.update_name');
  // メールアドレスの更新処理
  Route::post('/user/update/email', 'UserController@update_email')->name('user.update_email');
  // メールアドレスのリセットを確定
  Route::get('/user/update/email/{token}', 'UserController@reset_email')->name('user.reset_email');
  // パスワードの新規登録
  Route::post('/user/create/password', 'UserController@create_password')->name('user.create_password');
  // パスワードの更新
  Route::post('/user/update/password', 'UserController@update_password')->name('user.update_password');
  // 退会処理
  Route::post('/withdraw', 'UserController@withdraw')->name('user.withdraw');
  
  // =============================================
  // その他
  // =============================================
  // 指定のIDの最終更新日時を取得する
  Route::get('/updated/at/table', 'SystemController@get_updated_at');
  
  
});


