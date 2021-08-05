<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ForgetPasswordRequest extends FormRequest
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
        ];
    }
    public function messages(){
        return [
            "email.required"=>"Bạn chưa nhập email!!!","email.email"=>"Vui lòng nhập đúng định dạng email!!!",
        ];
    }
    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json(['status'=>false,'message'=>$validator->errors()]));
    }
}
