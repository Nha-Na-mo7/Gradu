<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
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
        return [
            'password_old' => "required|string|min:8|max:50|regex:/^[a-zA-Z0-9]+$/",
            'password' => "required|confirmed|string|min:8|max:50|regex:/^[a-zA-Z0-9]+$/"
        ];
    }
    
    public function messages()
    {
      
        return [
            'password_old.required' => '入力してください',
            'password_old.min' => '8文字以上で入力してください',
            'password_old.max' => '50文字以内で入力してください',
            'password_old.regex' => "半角英数字のみご利用いただけます",
          
            'password.required' => '入力してください',
            'password.confirmed' => 'パスワードと再入力が一致していません',
            'password.min' => '8文字以上で入力してください',
            'password.max' => '50文字以内で入力してください',
            'password.regex' => "半角英数字のみご利用いただけます",
        ];
      
    }
}
