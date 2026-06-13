<?php

namespace Modules\Clinic\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClinicsServiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = request()->id;

        $rules = [
            'duration_min'      => 'required|integer',
            'charges'           => 'required|numeric',
            'status'            => 'sometimes|boolean',
        ];

        // If multiVendor is enabled, require system_service_id
        if (multiVendor()) {
            $rules['system_service_id'] = 'required|integer|exists:system_service,id';
        }

        // Add advance payment validation: if enabled, value must be > 0
        // The field names may be: advance_payment_enabled (checkbox), advance_payment_value (number/percent)
        // Only validate if advance_payment_enabled is present and truthy
        if (
            ($this->has('advance_payment_enabled') && $this->input('advance_payment_enabled')) ||
            ($this->has('advance_payment_value') && $this->input('advance_payment_value') > 0)
        ) {
            $rules['advance_payment_value'] = [
                'required',
                'numeric',
                'gt:0'
            ];
        }

        switch (strtolower($this->getMethod())) {
            case 'post':
            case 'put':
            case 'patch':
                return $rules;
        }

        return [];
    }
     
    public function authorize(): bool
    {
        return true;
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
