<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistPost;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    
    private $PASSWORD_MIN_LENGTH =  8;
  
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // グローバル変数を記述しやすくするもの
        extract(get_object_vars($this));
      
        $messages = [
            'email.required' => '入力してください',
            'email.email' => 'メールアドレスの形式で入力してください',
            'email.unique' => '入力されたメールアドレスは既に登録されています',
            'email.max' => "100文字以内で入力してください",
            'password.required' => '入力してください',
            'password.min' => "${PASSWORD_MIN_LENGTH}文字以上で入力してください",
            'password.confirmed' => '入力されたパスワードと再入力が一致しません'
        ];
      
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
  
  
    // トレイトを使用しているRegisterControllerでregisteredメソッドの中身を実装して上書き。
    protected function registered(Request $request, $user)
    {
      return $user;
    }
}
