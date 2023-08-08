<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreUserRequest extends FormRequest
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
        $rules = [
            'first_name' => 'required|max:100',
            'email' => 'email|unique:users',
        ];

        if (request()->filled('password')) {
            $rules['current_password'] = 'required';
            $rules['password'] = 'required|same:confirm_password|min:6';
            $rules['confirm_password'] = 'required';
        }
        return $rules;
    }

    /**
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        ValidationMessageThrow::sendMessages($validator);
    }

}
