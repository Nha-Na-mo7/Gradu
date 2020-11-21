<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        
        // ログアウト、update処理が行われる
        Auth::logout();
        
        // ユーザーを削除する
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
  
  
  
  
    // =========================
    // メールアドレスの更新
    // =========================
  
  
  
  
    // =========================
    // パスワードの更新
    // =========================

    
    
    
    
}
