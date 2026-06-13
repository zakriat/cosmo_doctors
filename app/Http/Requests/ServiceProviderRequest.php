<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ServiceProvidersRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        switch (strtolower($this->getMethod())) {
            case 'post':
                return [
                    'name' => 'required|string|max:255',
                    'contact_number' => 'required|string|unique:service_providers,contact_number',
                    'contact_email' => 'required|string|unique:service_providers,contact_email',
                    'address.address_line_1' => 'string',
                    'address.address_line_2' => 'string',
                    'address.city' => 'string',
                    'address.state' => 'string',
                    'address.country' => 'string',
                    'address.postal_code' => 'string',
                    'payment_method' => 'required',
                    'status' => 'boolean',
                ];
                break;
            case 'put':
            case 'patch':

                return [
                    'name' => 'required|string',
                    'contact_number' => 'required|string',
                    'contact_email' => 'required|string',
                    'address.address_line_1' => 'string',
                    'address.address_line_2' => 'string',
                    'address.city' => 'string',
                    'address.state' => 'string',
                    'address.country' => 'string',
                    'address.postal_code' => 'string',
                    'payment_method' => 'required',
                    'status' => 'boolean',
                ];
                break;

            default:
                // code...
                break;
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => false,
            'message' => $validator->errors()->first(),
            'all_message' => $validator->errors(),
        ];

        if (request()->wantsJson() || request()->is('api/*')) {
            throw new HttpResponseException(response()->json($data, 422));
        }

        throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
    }
}
