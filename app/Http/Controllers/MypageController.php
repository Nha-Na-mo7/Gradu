<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MypageController extends Controller
{
    // マイページという名のアカウント設定項目一覧画面のController
    // マイページから行えること全般を扱います。
    /*
     * ・名前の変更
     * ・メールアドレスの更新
     * ・パスワードの新規追加・更新
     * ・Twitter連携の登録・解除などの処理
     * ・退会処理など
     */
  
    
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
        
        // リダイレクト処理はフロントエンドで行う
        
        return response()->json(['success'], 200);
        
      } catch (\Exception $e) {
        
        // ログアウト
        Auth::logout();
        // セッションを一度消してから再発行
        session()->invalidate();
        // csrfトークンを再生成
        session()->regenerateToken();
        
        Log::debug('退会処理の過程でエラーです。'. $e->getMessage());
        
        return response()->json(['error', 'エラーが発生しました。'], 500);
      }
    }
  
  
}
