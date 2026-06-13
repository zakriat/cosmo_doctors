<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class customerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (strtolower($this->getMethod())) {
            case 'post':
                return [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string',
                    'email' => 'required|string|unique:users,email',
                    'mobile' => 'required|string',
                    'gender' => 'string',
                    'nhs_number' => 'nullable|string|max:255',
                    'city_or_town' => 'nullable|string|max:255',
                    'county' => 'nullable|string|max:255',
                    'postcode' => 'nullable|string|max:255',
                    'password' => [
                        'required',
                        'string',
                        'min:8',             // must be at least 8 characters in length
                        'max:14',            // must not be more than 14 characters
                        'regex:/[a-z]/',      // must contain at least one lowercase letter
                        'regex:/[A-Z]/',      // must contain at least one uppercase letter
                        'regex:/[0-9]/',      // must contain at least one digit
                        'regex:/[@$!%*#?&]/', // must contain a special character
                    ],
                    'confirm_password' => 'required_with:password|same:password',
                ];
                break;
            case 'put':
            case 'patch':
                return [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string',
                    'email' => ['required', 'string', Rule::unique('users', 'email')->ignore($this->id)->whereNull('deleted_at')],
                    'mobile' => 'required|string',
                    'gender' => 'string',
                    'nhs_number' => 'nullable|string|max:255',
                    'city_or_town' => 'nullable|string|max:255',
                    'county' => 'nullable|string|max:255',
                    'postcode' => 'nullable|string|max:255',
                ];
                break;
            default:
                return [];
                break;
        }
    }

    public function messages()
    {
        return [
            'password.regex' => 'Password must contain at least one uppercase / one lowercase / one number and one symbol.',
            'password.min' => 'Password must be 8 to 14 characters.',
            'password.max' => 'Password must be 8 to 14 characters.',
            'confirm_password.required_with' => 'Please fill confirm password.',
            'confirm_password.same' => 'Confirm password must match the password.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
