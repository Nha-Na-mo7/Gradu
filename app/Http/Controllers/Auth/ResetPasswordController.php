<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     * パスワードリセット処理の後のリダイレクト先
     *
     * @var string
     */
    protected $redirectTo = '/';
    
    /**
     * Create a new Controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      $this->middleware('guest');
    }
    
    
    protected function resetPassword($user, $password)
    {
      $user->forceFill([
          'password' => bcrypt($password),
          'remember_token' => Str::random(60),
      ])->save();
      
    }
    
    
    protected function validator(array $data)
    {
      return Validator::make($data, [
          'token' => 'required',
          'email' => 'required|email:strict,dns,spoof|max:100',
          'password' => 'required|confirmed|string|min:8|max:50|regex:/^[a-zA-Z0-9]+$/',
      ]);
    }
    
    // public function showResetForm(Request $request, $token = null)
    //   {
    //     return view('auth.passwords.reset')->with(
    //         ['token' => $token, 'email' => $request->email]
    //     );
    //   }
}
