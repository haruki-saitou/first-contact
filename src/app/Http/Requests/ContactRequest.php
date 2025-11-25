<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class ContactRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'tel' => 'required|digits_between:10,11',
            'content' => 'required|max:2000',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];

    }

    public function messages()
    {
        return [
            'name.required' => '名前は必須項目です。',
            'name.string' => '名前は文字列で入力してください.',
            'name.max' => '名前は255文字以内で入力してください。',
            'email.required' => 'メールアドレスは必須項目です。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'email.string' => 'メールアドレスは文字列で入力してください。',
            'email.max' => 'メールアドレスは255文字以内で入力してください.',
            'tel.required' => '電話番号は必須項目です。',
            'tel.numeric' => '電話番号は数字のみで入力してください。',
            'tel.digits_between' => '電話番号は10桁または11桁の数字で入力してください。',
            'content.required' => 'お問い合わせ内容は必須項目です。',
            'content.max' => 'お問い合わせ内容は2000文字以内で入力してください。',
            'image_file.image' => 'アップロードされたファイルは画像ではありません。',
            'image_file.mimes' => '画像ファイルの形式はjpeg、png、jpg、gif、svgのいずれかの形式でアップロードしてください。',
            'image_file.max' => '画像ファイルのサイズは10MB以内でなければなりません。',
        ];
    }
}
