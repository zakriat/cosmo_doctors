<?php

namespace Modules\Clinic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SystemServiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = request()->id;
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'subcategory_id' => 'nullable',
            'type' => 'nullable|string|in:in_clinic,online',
            'is_video_consultancy' => 'nullable|in:0,1',
            'featured' => 'boolean',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'file_url' => 'nullable|image', // Keep image validation but remove size limit
            'custom_fields_data' => 'nullable|json',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => false
        ], 422));
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
