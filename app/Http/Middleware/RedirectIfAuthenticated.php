<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // ログイン中に、ログインしていない場合にしかアクセスできないリクエストを送信した時、
        // 代わりにユーザー情報を返すAPIにアクセスさせる。
        if (Auth::guard($guard)->check()) {
            Log::debug('RedirectifAuthenticated.php');
            return redirect()->route('user');
        }

        return $next($request);
    }
}
