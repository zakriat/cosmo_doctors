<?php

namespace Modules\Promotion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required|string|max:255'],
            'description' => ['required'],
            'coupon_code'=>['unique:promotions_coupon,coupon_code'],
            'use_limit'=>['required','min:1']

        ];
    }

    public function messages()
    {
        return [
            'coupon_code.unique'=>'coupon code must be unique.',
                'use_limit.min'=>'Use limit must be greater than or equal to 1'   ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
