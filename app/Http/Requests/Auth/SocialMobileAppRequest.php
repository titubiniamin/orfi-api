<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ValidationMessageThrow;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SocialMobileAppRequest extends FormRequest
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
        return [
            'first_name' => 'string|required|max:55',
            'last_name' => 'string|max:55',
            'email' => 'string|email|required',
            'avatar' => 'string',
            'social_id' => 'string',
            'social_account' => 'string',
        ];
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        ValidationMessageThrow::sendMessages($validator);
    }
}
