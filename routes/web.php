<?php

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
Route::get('/{any?}', 'IndexController@index')->where('any', '.+');

