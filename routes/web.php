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
// ③login/twitter/callbackへユーザー情報を返すので
// それをUserテーブルに保管する

// Twitter経由での認証を開始するURL
Route::get('/login/twitter', 'Auth\LoginController@redirectToTwitterProvider');
// Twitterアプリケーション側から情報が返ってくるURL
Route::get('/login/twitter/callback', 'Auth\LoginController@handleTwitterProviderCallback');


// ================
// other
// ================
// あらゆるURLはindex.blade.phpを返す
Route::get('/{any?}', fn() => view('index'))->where('any', '.+');

