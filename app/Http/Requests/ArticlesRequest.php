<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticlesRequest extends FormRequest
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
            'heading' => 'required|string',
            'content' => 'required|string',
            'user_id' => 'required',
            'category_id' => 'required'
        ];
    }

    public function messages()
    {
        return ['heading.required' => 'please enter heading !',
                'content.required' => 'please enter content !',
                'category_id.required' => 'Bro not selected id category !',
            ];
    }
}
