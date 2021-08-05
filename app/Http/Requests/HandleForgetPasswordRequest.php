<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class HandleForgetPasswordRequest extends FormRequest
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
            'password'=>'required|max:20|min:5',
        ];
    }
    public function messages(){
        return [
            "password.required"=>"Bạn chưa nhập mật khẩu!!!","password.max"=>"Mật khẩu bạn nhập phải nhỏ hơn 20 kí tự!!!","password.min"=>"Mật khẩu bạn nhập phải lớn hơn 5 kí tự!!!"
        ];
    }
    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json(['status'=>false,'message'=>$validator->errors()]));
    }
}
