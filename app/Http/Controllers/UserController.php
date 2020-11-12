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
}
