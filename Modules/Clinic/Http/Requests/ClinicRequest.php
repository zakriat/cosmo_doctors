<?php

namespace Modules\Clinic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClinicRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Only use ID for validation if it's a valid integer (update mode)
        $id = request()->id && is_numeric(request()->id) ? request()->id : null;
        
        return [
             'name' => [
                'required',
                Rule::unique('clinic', 'name')
                    ->ignore($id)
                    ->whereNull('deleted_at')
             ],
             'email' => [
                'required',
                'string',
                Rule::unique('clinic', 'email')
                    ->ignore($id)
                    ->whereNull('deleted_at')
             ],
             'speciality' => 'required|exists:system_service_category,id',
            // 'address' => 'required|string',
            // 'pincode' => 'required',
            // 'contact_number' => 'required|string',
            // 'email' => 'required|email',


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
