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


// トップページ
Route::get('/', 'IndexController@index')->name('home.index');
// =========================
// 認証系(認証なしで利用可能)
// =========================
// 認証系ルート
Auth::routes();
// ログアウト
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');


// ================
// Twitter認証用
// ================
// Twitter経由での認証を開始するURL
Route::get('/twitter/auth/begin', 'TwitterController@redirectToTwitterProvider')->name('twitter.begin');
// Twitterアプリケーション側から情報が返ってくるコールバックURL
Route::get('/twitter/auth/callback', 'TwitterController@handleTwitterProviderCallback');


// ===========================================
// 会員ページ・認証必須のもの
// ===========================================
Route::group(['middleware' => 'auth'], function (){
  // =============================================
  // 銘柄関連
  // =============================================
  // 指定の通貨のカラムを取得する
  Route::get('/brand/{brand_id}', 'BrandController@get_brands');
  // 通貨カラムを全て取得する
  Route::get('/brand{any?}', 'BrandController@get_brands')->where('any', '.+')->name('get_brands');
  
  // =============================================
  // Twitterアカウント一覧画面関連
  // =============================================
  // アカウント一覧画面/テーブルからアカウント情報を取得
  Route::get('/accounts/list', 'TwitterAccountListController@get_accounts_list');
  // アカウントコンポーネント/指定したユーザーの新着ツイートを取得
  Route::get('/accounts/tweet/{tweet_id}', 'TwitterAccountListController@accounts_tweet')->name('accounts.tweet');
  // アカウントコンポーネント/認証中ユーザーのフォローリストを取得
  Route::get('/accounts/followlist', 'TwitterAccountListController@get_follow_target_list');
  
  // 認証ユーザーの自動フォローフラグを切り替える
  Route::post('/accounts/autofollowflg', 'TwitterAccountListController@toggle_auto_follow_flg');
  // 指定したTwitterAccountListアカウントをフォローする
  Route::post('/accounts/follow', 'TwitterAccountListController@accounts_follow');
  // 指定したTwitterアカウントのフォローを外す
  Route::post('/accounts/destroy', 'TwitterAccountListController@accounts_destroy');
  
  // Twitterとの連携を解除する
  Route::post('/accounts/un_linkage', 'TwitterController@un_linkage');
  
  // 一覧画面のビュー返却
  Route::get('/accounts{any?}', 'TwitterAccountListController@index')->name('accounts.index')->where('any', '.+');
  
  // =============================================
  // GoogleNews関連
  // =============================================
  // 指定したワードでGoogleNewsAPIを使用し、ニュースを取得する
  Route::get('/news/get', 'GoogleNewsController@get_news');
  // ビューを返却
  Route::get('/news{any?}', 'GoogleNewsController@index')->name('news.index')->where('any', '.+');
  
  // =============================================
  // トレンド一覧表示関連
  // =============================================
  // 過去1時間or1日or1週間のツイート数を取得する
  Route::get('/tweet/count', 'CoinCheckController@get_tweet_count');
  // 指定の通貨の24時間以内での最高・最安取引価格情報カラムを取得する
  Route::get('/transaction/price/{brand_id}', 'CoinCheckController@get_transaction_price');
  // 通貨の24時間以内での最高・最安取引価格情報カラムを全て取得する
  Route::get('/transaction/price{any?}', 'CoinCheckController@get_transaction_price')->where('any', '.+');
  
  // トレンド画面のビューを返却
  Route::get('/trends{any?}', 'CoinCheckController@index')->where('any', '.+')->name('trend.index');
  
  // =============================================
  // ユーザー関連
  // =============================================
  // ログインしているユーザー情報を取得する
  Route::get('/user/info', 'UserController@auth_user');
  // ログインしているかをチェックする
  Route::get('/user/auth/check', 'UserController@auth_check');
  // メールアドレスの更新処理
  Route::post('/user/update/email', 'UserController@update_email');
  // メールアドレスのリセットを確定
  Route::get('/user/update/email/{token}', 'UserController@reset_email');
  // パスワードの新規登録
  Route::post('/user/create/password', 'UserController@create_password');
  // パスワードの更新
  Route::post('/user/update/password', 'UserController@update_password');
  // 退会処理
  Route::post('/withdraw', 'UserController@withdraw')->name('user.withdraw');
  
  // マイページ・アカウント設定画面のビューを返却する
  Route::get('/mypage{any?}', 'MypageController@index')->name('mypage.index')->where('any', '.+');
  
  // =============================================
  // その他
  // =============================================
  // 指定のIDの最終更新日時を取得する
  Route::get('/updated/at/table', 'SystemController@get_updated_at');
  // システムエラーの時
  Route::get('/500', 'IndexController@error')->name('home.error');
});

// ==========================================================
// 上記以外の全てのページに対してはNotFoundとしてerror.blade.phpを返す
// ==========================================================
// error.blade.php
Route::get('/{any?}', 'IndexController@error')->where('any', '.+')->name('home.error');

