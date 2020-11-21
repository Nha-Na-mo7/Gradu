<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMailRequest extends FormRequest
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
            'email' => "required|email:strict,dns,spoof|max:100",
        ];
    }
    
    public function messages()
    {

      return [
          'email.required' => '入力してください',
          'email.email' => '正しいメールアドレスを入力してください',
          'email.max' => "100文字以内で入力してください",

      ];
      
    }
}
