<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|max:255',
            'tel' => 'required|numeric|digits_between:10,11',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '名前は必須項目です。',
            'name.string' => '名前は文字列で入力してください。',
            'name.max' => '名前は255文字以内で入力してください。',
            'email.required' => 'メールアドレスは必須項目です。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'email.string' => 'メールアドレスは文字列で入力してください。',
            'email.max' => 'メールアドレスは255文字以内で入力してください。',
            'tel.required' => '電話番号は必須項目です。',
            'tel.numeric' => '電話番号は数字のみで入力してください。',
            'tel.digits_between' => '電話番号は10桁または11桁の数字で入力してください。',
            'password.required' => 'パスワードは必須項目です。',
            'password.string' => 'パスワードは文字列で入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.confirmed' => 'パスワード確認が一致しません。',
            'password_confirmation.required' => 'パスワード確認は必須項目です。',
            'password_confirmation.string' => 'パスワード確認は文字列で入力してください。',
            'password_confirmation.min' => 'パスワード確認は8文字以上で入力してください。',
        ];
    }
}