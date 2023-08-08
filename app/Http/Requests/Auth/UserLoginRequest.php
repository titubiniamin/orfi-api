<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ValidationMessageThrow;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserLoginRequest extends FormRequest
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
    public function rules() : array
    {
        $rules = [];
        $rules['password'] = 'required|string|min:6';
        $rules['remember_me'] = 'boolean';

        if (preg_match("/[a-zA-Z]|@/", request()->email_or_phone)) {
            $rules['email_or_phone'] = ['required','email'];
        } else {
            $rules['email_or_phone'] = ['required','regex:/(^(\+88|0088)?(01){1}[23456789]{1}(\d){8})$/', 'max:11', 'min:11'];
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() : array
    {
        return [
            'email_or_phone.regex' => 'Phone number always start with 01 and 11 digits.',
            'email_or_phone.max' => 'Phone number not more 11 digits.',
            'email_or_phone.min' => 'Phone number not less then 11 digits.',
            'email_or_phone.email' => 'Email will be a valid email address.',
        ];
    }

    /**
     * @param Validator $validator
     */
    protected
    function failedValidation(Validator $validator)
    {
        ValidationMessageThrow::sendMessages($validator);
    }
}
