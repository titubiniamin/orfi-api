<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ValidationMessageThrow;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class UpdateUserProfileRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        $rules = [];
        if (request()->filled('first_name')) $rules['first_name'] = 'string|max:100';
        if (request()->filled('last_name')) $rules['last_name'] = 'string|max:100';
        if (request()->filled('email')) $rules['email'] =  'required|email|unique:users,email,'.$this->id;
        if (request()->filled('date_of_birth') && $this->get('date_of_birth') != null) $rules['date_of_birth'] = 'date';
        if (request()->filled('phone')) $rules['phone'] = 'string';
        if (request()->filled('address')) $rules['address'] = 'string';
        if (request()->filled('password')){
            $rules['password'] = 'string|min:6|same:confirm_password';
            $rules['confirm_password'] = 'required|string|min:6';
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
