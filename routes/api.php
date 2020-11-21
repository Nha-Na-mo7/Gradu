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

// =============================================
// 認証関連
// =============================================
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
// 退会処理
Route::post('/withdraw', 'UserController@withdraw')->name('user.withdraw');



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
// アカウント一覧画面/テーブルからアカウント情報を取得
Route::get('/accounts/index', 'TwitterAccountListController@accounts_index')->name('accounts.index');
// アカウントコンポーネント/指定したユーザーの新着ツイートを取得
Route::get('/accounts/tweet/{tweet_id}', 'TwitterAccountListController@accounts_tweet')->name('accounts.tweet');


// 認証ユーザーの自動フォローフラグを切り替える
Route::post('/accounts/autofollowflg', 'TwitterAccountListController@toggle_auto_follow_flg')->name('accounts.follow');
// 指定したTwitterAccountListアカウントをフォローする
Route::post('/accounts/follow', 'TwitterAccountListController@accounts_follow')->name('accounts.follow');
// 指定したTwitterアカウントのフォローを外す
// Route::get('/accounts/get', 'TwitterAccountListController@')->name('');


// =============================================
// GoogleNews関連
// =============================================
// 指定したワードでGoogleNewsAPIを使用し、ニュースを取得する
Route::get('/news/get', 'GoogleNewsController@get_news')->name('get_news');


// =============================================
// トレンド一覧表示関連
// =============================================
// 過去1時間or1日or1週間のツイート数を取得する
Route::get('/tweet/count', 'CoinCheckController@get_tweet_count');
// 指定の通貨の24時間以内での最高・最安取引価格情報を取得する
Route::get('/transaction/price', 'CoinCheckController@get_transaction_price');




// =============================================
// その他
// =============================================
// 指定のIDの最終更新日時を取得する
Route::get('/updated/at/table', 'SystemController@get_updated_at');
//ログインしているユーザー情報を取得する
// Route::get('/user', fn() => Auth::user())->name('user');
Route::get('/user', 'UserController@auth_user')->name('user');


// トークンリフレッシュAPI
Route::get('/reflesh-token', function (Request $request) {
  $request->session()->regenerateToken();
  
  return response()->json();
});



