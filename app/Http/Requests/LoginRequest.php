<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
            'email'=>'required|email',
            'password'=>'required|max:20|min:5',
        ];
    }
    public function messages(){
        return [
            "email.required"=>"Bạn chưa nhập email!!!","email.email"=>"Vui lòng nhập đúng định dạng email!!!",
            "password.required"=>"Bạn chưa nhập mật khẩu!!!","password.max"=>"Mật khẩu bạn nhập phải nhỏ hơn 20 kí tự!!!","password.min"=>"Mật khẩu bạn nhập phải lớn hơn 5 kí tự!!!"
        ];
    }
    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json(['status'=>false,'message'=>$validator->errors()]));
    }
}
