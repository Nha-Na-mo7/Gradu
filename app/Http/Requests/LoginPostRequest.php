<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginPostRequest extends FormRequest
{
    private $PASSWORD_MIN_LENGTH = 8;
  
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        extract(get_object_vars($this));
    
        return [
            'email' => "required|email|max:100}",
            'password' => "required|min:{$PASSWORD_MIN_LENGTH}"
        ];
    }
    
    public function messages()
    {
        // グローバル変数を記述しやすくするもの
        extract(get_object_vars($this));
    
        return [
            'email.required' => '入力してください',
            'email.email' => 'メールアドレスの形式で入力してください',
            'email.max' => "100文字以内で入力してください",
            'password.required' => '入力してください',
            'password.min' => "{$PASSWORD_MIN_LENGTH}文字以上で入力してください",
        ];
        
    }
}
