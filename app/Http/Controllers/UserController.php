<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePasswordRequest;
use App\Http\Requests\UpdateMailRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUsernameRequest;
use App\Models\EmailReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use function Psy\debug;

class UserController extends Controller
{
    public function __construct(){
      $this->middleware('auth');
    }
  
    // ============================
    // 現在認証中のユーザー情報を返却する
    // ============================
    public function auth_user() {
      Log::debug('UserController.auth_user 認証中のユーザー情報を返却します。');
      Log::debug('?!');
      return Auth::user();
    }
    //
    // // ============================
    // // 現在認証中かをチェックする
    // // ============================
    // public function auth_check() {
    //   Log::debug('auth_check::'.Auth::check());
    //   if(Auth::check()){
    //     return response()->json([], 200);
    //   }else{
    //     return response(419);
    //   }
    // }
    
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
      try {
        $user = Auth::user();
      
        $new_email = $request->email;
        Log::debug('変更後のemail :'.$new_email);
        
        $result = $this->send_change_email_link($user->id, $new_email);
        
        Log::debug('新しいメールアドレス宛にメールを送信しました。');
      
        return response(200);
      }catch (\Exception $e) {
        Log::debug('エラーが発生しました。'. $e->getMessage());
        return response()->json(['errors'], 500);
      }
    }
    
    // ================================
    // メールアドレス変更確認メールを送信する
    // ================================
    public function send_change_email_link($user_id, $new_email) {
      Log::debug('UserController.send_change_email_link 変更確認メールの送信');
  
      // トークン生成
      $token = hash_hmac('sha256', Str::random(40) . $new_email, config('app.key'));
  
      Log::debug('作成したトークンをDBに保存します。');
      // トークンをDBに保存
      $param = [];
      $param['user_id'] = $user_id;
      $param['new_email'] = $new_email;
      $param['token'] = $token;
 
      $email_reset = EmailReset::create($param);
      
      // リセットメールを送信する
      Log::debug($new_email.'宛にリセットメールを送信します。');
      $email_reset->send_email_reset_notification($token);
      
    }
  
  
    // ================================
    // メールアドレスの更新を確定させる
    // ================================
    public function reset_email(Request $request, $token) {
      Log::debug('============================');
      Log::debug('reser_email メアド更新完了手続き');
      Log::debug('============================');
      // トークンが格納されたレコードを取得する
      Log::debug('トークンが格納されたレコードがあるか確認します。');
      $email_resets = DB::table('email_resets')
          ->where('token', $token)
          ->first();
      
      // トークンが存在している && 有効期限以内かをチェック
      if ($email_resets && !$this->token_expired($email_resets->created_at)){
        Log::debug('有効期限以内でした。更新手続きを開始します。');
        // ユーザーのメアドを更新
        $user = User::find($email_resets->user_id);
        $user->email = $email_resets->new_email;
        $user->save();
        
        Log::debug('トークンレコードを削除します');
        // トークンテーブルからレコードを削除
        DB::table('email_resets')
            ->where('token', $token)
            ->delete();
        
        Log::debug('更新手続きは完了しました。マイページにリダイレクトします。');
        return redirect('/mypage')->with('flash_message', 'メールアドレスの更新が完了しました。');
        
      }else{
        // 有効期限オーバーになったレコードは削除する
        if($email_resets){
          Log::debug('有効期限オーバーです。');
          DB::table('email_resets')
              ->where('token', $token)
              ->delete();
        }
        
        Log::debug('更新手続きに失敗しました...。マイページにリダイレクトします。');
        return redirect('/mypage')->with('flash_message', 'メールアドレスの更新に失敗しました。');
      }
    }
  
    
    // =========================================
    // トークンが有効期限オーバーかをチェックする
    // =========================================
    protected function token_expired($createdAt)
    {
      // トークンの有効期限は60分に設定
      $expires = 60 * 60;

      // トークンの作成時刻 + $expiresで設定した時刻と現在時刻を比較する。
      // isPastでcreated_at + $expiredをオーバーしていた場合、falseを返す。
      return Carbon::parse($createdAt)->addSeconds($expires)->isPast();
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
