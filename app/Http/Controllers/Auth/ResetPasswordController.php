<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\validator;
use Illuminate\Support\Str;

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
    
    public function reset(Request $request)
    {
      $validate =  $this->validator($request->all());
      
      // バリデーション失敗時
      if($validate->fails()) {
        return new JsonResponse($validate->errors());
      }
      $response = $this->broker()->reset(
          $this->credentials($request), function ($user, $password){
            $this->resetPassword($user, $password);
          }
      );
      
      return $response == Password::PASSWORD_RESET
          ? $this->sendResetResponse($request, $response)
          : $this->sendResetFailedResponse($request, $response);
    }
    
    protected function resetPassword($user, $password)
    {
      $user->forceFill([
          'password' => bcrypt($password),
          'remember_token' => Str::random(60),
      ])->save();
    }
    
    protected function sendResetResponse(Request $request, $response)
    {
      return new JsonResponse('password Reset');
    }
    
    protected function sendResetFailedResponse(Request $request, $response)
    {
      return new JsonResponse($response);
    }
    
    protected function validator(array $data)
    {
      return Validator::make($data, [
          'token' => 'required',
          'email' => 'required|email',
          'password' => 'required|confirmed|min:8',
      ]);
    }
    
    // public function showResetForm(Request $request, $token = null)
    //   {
    //     return view('auth.passwords.reset')->with(
    //         ['token' => $token, 'email' => $request->email]
    //     );
    //   }
}
