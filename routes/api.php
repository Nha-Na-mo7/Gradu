<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// ===============
// 認証関連
// ===============
// 会員登録
Route::post('/register', 'Auth\RegisterController@register')->name('register');
// ログイン
Route::post('/login', 'Auth\LoginController@login')->name('login');
// ログアウト
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
// パスワードリセットメール送信
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('api.password.email');
// パスワードリセット
Route::post('/password/reset/{token}', 'Auth\ResetPasswordController@reset')->name('api.password.reset');
// パスワードリセット
// Route::post('"/password/reset/{token?}"', 'Auth\ResetPasswordController@showResetForm')->name('api.password.showResetForm');

//ログインしているユーザー情報を取得するAPI
Route::get('/user', fn() => Auth::user())->name('user');


// ===============
// 銘柄関連
// ===============
// 通貨カラムを全て取得する
Route::get('/brand', 'BrandController@get_brands')->name('get_brands');
// 指定の通貨のカラムを取得する
Route::get('/brand/{brand_id}', 'BrandController@get_brands')->name('get_brands.brandid');


// ===============
// Twitter関連
// ===============
// TODO 開発用 レートリミットの使用状況取得
Route::get('/twitter/check_limit_status', 'TwitterController@check_limit_status')->name('twitter.check_limit_status');
// バッチ用・Twitterアカウント一覧取得
Route::get('/twitter/index', 'TwitterController@search_accounts')->name('twitter.search_accounts');

// アカウント一覧画面/テーブルからアカウント情報を取得
Route::get('/accounts/index', 'TwitterController@accounts_index')->name('accounts.index');
// アカウントコンポーネント/指定したユーザーの新着ツイートを取得
Route::get('/accounts/tweet/{tweet_id}', 'TwitterController@accounts_tweet')->name('accounts.tweet');


// 認証ユーザーの自動フォローフラグを切り替える
Route::post('/accounts/autofollowflg', 'TwitterController@toggle_auto_follow_flg')->name('accounts.follow');
// 指定したTwitterアカウントをフォローする
Route::post('/accounts/follow', 'TwitterController@accounts_follow')->name('accounts.follow');
// 指定したTwitterアカウントのフォローを外す
// Route::get('/accounts/get', 'TwitterController@')->name('');
// Twitterアカウントの自動フォローを開始する

// ===============
// GoogleNews関連
// ===============
// 指定したワードでGoogleNewsAPIを使用し、ニュースを取得する
Route::get('/news/get', 'GoogleNewsController@get_news')->name('get_news');




// ===============
// その他
// ===============
// 指定のIDの最終更新日時を取得する
Route::get('/updated/at/table', 'SystemController@get_updated_at');



// トークンリフレッシュAPI
Route::get('/reflesh-token', function (Request $request) {
  $request->session()->regenerateToken();
  
  return response()->json();
});



