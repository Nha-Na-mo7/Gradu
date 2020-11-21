<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      return [
          'password' => "required|confirmed|string|min:8|max:50|regex:/^[a-zA-Z0-9]+$/"
      ];
    }
    
    public function messages()
    {
      
      return [
          'password.required' => '入力してください',
          'password.confirmed' => 'パスワードと再入力が一致していません',
          'password.min' => '8文字以上で入力してください',
          'password.max' => '50文字以内で入力してください',
          'password.regex' => "半角英数字のみご利用いただけます",
      ];
      
    }
}
