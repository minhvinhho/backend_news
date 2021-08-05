<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistrationRequest extends FormRequest
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
            'name'=>'required|min:5|max:20',
            'role_id'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|max:20|min:5',
            'avatar_link'=>'required'
        ];
    }
    public function messages(){
        return [
            "name.required"=>"Bạn chưa nhập tên!!!","name.max"=>"Tên bạn nhập phải nhỏ hơn 20 kí tự!!!","name.min"=>"Tên bạn nhập phải lớn hơn 5 kí tự!!!",
            "email.required"=>"Bạn chưa nhập email!!!","email.email"=>"Vui lòng nhập đúng định dạng email!!!","email.unique"=>"Email bạn nhập đã trùng!!!",
            "password.required"=>"Bạn chưa nhập mật khẩu!!!","password.max"=>"Mật khẩu bạn nhập phải nhỏ hơn 20 kí tự!!!","password.min"=>"Mật khẩu bạn nhập phải lớn hơn 5 kí tự!!!"
        ];
    }
    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json(['status'=>false,'message'=>$validator->errors()]));
    }
}
