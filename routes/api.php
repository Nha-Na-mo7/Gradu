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
// GoogleNews関連
// ===============
Route::get('/news/get', 'GoogleNewsController@get_news')->name('get_news');



