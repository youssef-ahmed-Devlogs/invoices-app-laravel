<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProduct extends FormRequest
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
    public function rules()
    {
        return [
            'product_name' => ['required', 'min:2', 'max:100'],
            'section_id' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'product_name.required' => 'يجب ادخال اسم المنتج.',
            'product_name.min' => 'يجب أن يكون اسم المنتج 3 حروف على الاقل.',
            'section_id.required' => 'يجب اختيار تصنيف.'
        ];
    }
}
