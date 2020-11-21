<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePasswordRequest;
use App\Http\Requests\UpdateMailRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUsernameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function Psy\debug;

class UserController extends Controller
{
    // ============================
    // 現在認証中のユーザー情報を返却する
    // ============================
    public function auth_user() {
      // Log::debug('UserController.auth_user 認証中のユーザー情報を返却します。');
      return Auth::user();
    }
    
    // ======================
    // 退会する
    // ======================
    public function withdraw()
    {
      try {
        // 認証済みユーザーを取得
        $user = Auth::user();
        
        // 先にログアウトしてから後続の処理を行う
        Auth::logout();
        
        // ユーザーを削除
        $user->delete();
        
        // セッションを一度消してから再発行
        session()->invalidate();
        // csrfトークンを再生成
        session()->regenerateToken();
        
        session()->flash('退会処理が完了しました。');
        
        // リダイレクト処理はフロントエンドで行う
        return response(200);
        
      } catch (\Exception $e) {
        session()->flash('退会処理の途中でエラーが発生しました。');
        // ログアウト
        Auth::logout();
        // セッションを一度消してから再発行
        session()->invalidate();
        // csrfトークンを再生成
        session()->regenerateToken();
        
        Log::debug('退会処理の過程でエラーです。'. $e->getMessage());
        return response(500);
      }
    }
    
    // =========================
    // ユーザーネームの更新
    // =========================
    // リクエストクラスを用意するべきか
    public function update_name(UpdateUsernameRequest $request) {
      try {
        $user = Auth::user();
        
        $new_name = $request['name'];
        Log::debug('変更後のユーザーネーム: '.$new_name);
        
        $user->name = $new_name;
        $user->save();
        
        Log::debug('ユーザーネームの変更完了しました。');
        return response(200);
      }catch(\Exception $e) {
        Log::debug('エラーが発生しました。'. $e->getMessage());
        return response(500);
      }
    }
    
    // =========================
    // メールアドレスの更新
    // =========================
    // 変更後に確認メールを送信する。
    public function update_email(UpdateMailRequest $request) {
      try {
        $user = Auth::user();
        
        $new_mail = $request['email'];
        Log::debug('変更後のemail :'.$new_mail);
        
        Log::debug('新しいメールアドレス宛にメールを送信しました。');
        
        return response(200);
      }catch (\Exception $e) {
        Log::debug('エラーが発生しました。'. $e->getMessage());
        return response(500);
      }
    }
  
    
    // =========================
    // パスワードを新しく設定する
    // =========================
    // Twitterで新規登録した場合、パスワードは空の状態でユーザーが作成される。
    // そのため更新処理とは別に新規パスワード作成を用意する。
    public function password_create(CreatePasswordRequest $request) {
      try {
        
        
        
        return response(200);
      }catch (\Exception $e){
        Log::debug('エラーが発生しました。'. $e->getMessage());
        return response(500);
      }
    }
    
    // =========================
    // パスワードを更新する
    // =========================
    // こちらは純粋なパスワードの更新処理メソッド。
    // TODO リクエストクラスを
    public function password_update(UpdatePasswordRequest $request) {
      try {
        $user = Auth::user();
        
        $data = $request->all();
        
        return response(200);
      } catch (\Exception $e){
        Log::debug('エラーが発生しました。'. $e->getMessage());
        return response(500);
      }
    }

    
    
    
    
}
