<?php

namespace Modules\Slider\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
{
    public function rules()
    {
        return [
            // 'name' => 'required|string|max:255',
            // 'type' => 'required|integer', // since it's coming from <select> IDs
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            // 'name.required' => 'The name field is required.',
            // 'name.string'   => 'The name must be a string.',
            // 'name.max'      => 'The name may not be greater than 255 characters.',
            // 'type.required' => 'The type field is required.',
            // 'type.integer'  => 'The type must be a valid selection.',
        ];
    }
}
