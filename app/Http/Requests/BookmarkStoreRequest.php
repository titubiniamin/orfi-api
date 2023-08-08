<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class BookmarkStoreRequest extends FormRequest
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
            'index_id' => 'required|string',
        ];
    }

    /**
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        ValidationMessageThrow::sendMessages($validator);
    }

}
