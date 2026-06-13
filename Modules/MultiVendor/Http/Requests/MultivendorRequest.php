<?php

namespace Modules\MultiVendor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MultivendorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,intersex',
            'mobile' => 'required|string',
            // Address and location requirements
            'address' => 'required|string|max:500',
            'country' => 'required|integer|exists:countries,id',
            'state' => 'required|integer|exists:states,id',
            'city' => 'required|integer|exists:cities,id',
            'pincode' => 'required|string'
        ];

        // Handle email validation for create vs update
        if ($this->isMethod('post')) {
            // Create - email must be unique
            $rules['email'] = 'required|string|email|unique:users,email';
            $rules['password'] = 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/';
            $rules['confirm_password'] = 'required|same:password';
        } else {
            // Update - email must be unique except for current record
            $rules['email'] = 'required|string|email|unique:users,email,' . $this->route('multivendor');
        }

        return $rules;
    }

    /**
     * Custom validation messages for this request.
     */
    public function messages(): array
    {
        return [
            'gender.required' => __('validation.required', ['attribute' => __('clinic.lbl_gender')]),
            'gender.in' => __('validation.in', ['attribute' => __('clinic.lbl_gender')]),
            'address.required' => __('validation.required', ['attribute' => __('clinic.lbl_address')]),
            'country.required' => __('validation.required', ['attribute' => __('Clinic.lbl_country')]),
            'state.required' => __('validation.required', ['attribute' => __('Clinic.lbl_state')]),
            'city.required' => __('validation.required', ['attribute' => __('Clinic.lbl_city')]),
            'pincode.required' => __('validation.required', ['attribute' => __('clinic.lbl_postal_code')]),
            'country.exists' => __('validation.exists', ['attribute' => __('Clinic.lbl_country')]),
            'state.exists' => __('validation.exists', ['attribute' => __('Clinic.lbl_state')]),
            'city.exists' => __('validation.exists', ['attribute' => __('Clinic.lbl_city')]),
            'password.regex' => __('clinic.password_regex'),
            'password.min' => __('clinic.password_min'),
            'password.max' => __('clinic.password_max'),
            'confirm_password.required_with' => __('clinic.confirm_password_required_with'),
            'confirm_password.same' => __('clinic.confirm_password_same'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
