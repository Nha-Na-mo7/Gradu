<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePasswordRequest;
use App\Http\Requests\UpdateMailRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUsernameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
    
    // =========================
    // ユーザーネームの更新
    // =========================
    // リクエストクラスを用意するべきか
    public function update_name(UpdateUsernameRequest $request) {
      Log::debug('UserController.update_name ユーザーネームの更新');
      try {
        $user = Auth::user();
        
        $new_name = $request->name;
        Log::debug('変更後のユーザーネーム: '.$new_name);
        
        $user->name = $new_name;
        $user->save();
        
        Log::debug('ユーザーネームの変更完了しました。');
        return response(200);
      }catch(\Exception $e) {
        Log::debug('エラーが発生しました。'. $e->getMessage());
        return response()->json(['errors'], 500);
      }
    }
    
    // =========================
    // メールアドレスの更新
    // =========================
    // 変更後に確認メールを送信する。
    public function update_email(UpdateMailRequest $request) {
      Log::debug('UserController.update_mail メールアドレスの更新');
      // try {
      //   $user = Auth::user();
      //
      //   $new_email = $request->email;
      //   Log::debug('変更後のemail :'.$new_email);
      //
      //   // 旧メールアドレスと同じメールアドレスが送信された場合は
      //   if($user->email === $new_email) {
      //     return response();
      //   }
      //   Log::debug('新しいメールアドレス宛にメールを送信しました。');
      //
      //   return response(200);
      // }catch (\Exception $e) {
      //   Log::debug('エラーが発生しました。'. $e->getMessage());
      //   return response()->json(['errors'], 500);
      // }
      return response()->json(200);
    }
    
    // ================================
    // メールアドレス変更確認メールを送信する
    // ================================
    public function send_change_mail() {
    
    }
    
  
  
  
  
  
  
    
    // =========================
    // パスワードを新しく設定する
    // =========================
    // Twitterで新規登録した場合、パスワードは空の状態でユーザーが作成される。
    // そのため更新処理とは別に新規パスワード作成を用意する。
    public function create_password(CreatePasswordRequest $request) {
      Log::debug('UserController.password_create パスワード新規作成');
      try {
        $user = Auth::user();
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        Log::debug('パスワードを新規登録しました。');
        
        return response(200);
        
      }catch (\Exception $e){
        
        Log::debug('エラーが発生しました。'. $e->getMessage());
        return response()->json(['errors'], 500);
      }
    }
    
    // =========================
    // パスワードを更新する
    // =========================
    // こちらは純粋なパスワードの更新処理メソッド。
    public function update_password(UpdatePasswordRequest $request) {
      Log::debug('UserController.password_update パスワードの更新');
      // 入力されたパスワードが同じ者なら弾く
      // if($request->old_password === $request->password) {
      //   return response()->json(
      //       ['errors' =>
      //           ['password' =>
      //               ['入力されたパスワードが同じです']
      //           ]
      //       ], 422);
      // }
      
      try {
        // 更新の場合は旧パスワードと一致するかを確認する工程が入る
        $user = Auth::user();
        
        // Hash::makeでは毎回違うハッシュ値になるので比較できない
        // Hash::checkを使ってDBのパスワードと比較する
        if(!Hash::check($request->get('old_password'), $user->password)) {
          Log::debug('old_passwordは一致しませんでした。422をreturnします。');
          return response()->json(
              ['errors' =>
                  ['old_password' =>
                      ['現在のパスワードが一致しません']
                  ]
              ], 422);
        }
        
        Log::debug('old_passwordが認証ユーザーのテーブルのパスワードと一致しました。更新処理を開始します。');
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        Log::debug('パスワードが更新されました');
        
        return response(200);
        
      } catch (\Exception $e){
        Log::debug('エラーが発生しました。'. $e->getMessage());
        return response()->json(['errors'], 500);
      }
    }
  
    
    // ======================
    // 退会
    // ======================
    public function withdraw()
    {
      Log::debug('UserController.withdraw 退会処理');
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
        return response()->json(['errors'], 500);
      }
    }
}
