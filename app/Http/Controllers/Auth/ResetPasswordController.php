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
    
    public function reset(Request $request)
    {
      $validate =  $this->validator($request->all());
      Log::debug('バリデーション');
  
      // バリデーション失敗時
      if($validate->fails()) {
        // JsonResponseの引数に422を追加してバリデーションエラー扱いにする。(引数なしだと200扱いになるため)
        return new JsonResponse($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
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
    
    // バリデーションを通過後
    // DBにメールアドレスがない、トークンが無効などの場合、422を返却
    protected function sendResetFailedResponse(Request $request, $response)
    {
      // vueコンポーネントで参照するため、キーヴァリューの形式。さらにここで日本語化させる。
      return new JsonResponse(['reset' => __($response)], Response::HTTP_UNPROCESSABLE_ENTITY);
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
