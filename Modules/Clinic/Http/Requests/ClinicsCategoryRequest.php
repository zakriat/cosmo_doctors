<?php

namespace Modules\Clinic\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClinicsCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('category') ?? request()->id;
        switch (strtolower($this->getMethod())) {
            case 'post':
                return [
                    'name' => 'required|string|max:255|unique:clinics_categories,name',
                    'status' => 'boolean',
                    'file_url' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                ];
                break;
            case 'put':
            case 'patch':
                return [
                    'name' => 'required|string|max:255|unique:clinics_categories,name,'.$id,
                    'status' => 'boolean',
                    'file_url' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                    'remove_image' => 'nullable|in:0,1',
                ];
                break;
        }

        return [];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.unique' => 'This category name already exists. Please choose a different name.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'status.boolean' => 'Status must be a valid boolean value.',
            'file_url.image' => 'The uploaded file must be an image.',
            'file_url.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif.',
            'file_url.max' => 'The image size cannot exceed 2MB.',
            'remove_image.in' => 'Remove image value must be 0 or 1.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ];

        // Always return JSON for AJAX requests or API calls
        if (request()->wantsJson() || request()->is('api/*') || request()->ajax()) {
            throw new HttpResponseException(response()->json($data, 422));
        }

        // Only redirect for non-AJAX requests
        throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
    }
}
