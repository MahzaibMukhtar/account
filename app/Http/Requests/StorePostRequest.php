<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'=>'required|max:170|alpha',
            'email'=>'required|email|max:100',
            'gender'=>'required|max:10|alpha',
            'age'=>'required|min:1|numeric',
            'password'=>'required|min:7|',
            'confirm password'=>'|same:password',
            'email_verification_code'=>'max:40',
            'verified'=>'boolean'

        ];
    }
}
