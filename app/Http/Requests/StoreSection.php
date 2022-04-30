<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSection extends FormRequest
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
            'section_name' => ['required', 'max:100', 'min:3', 'unique:sections']
        ];
    }

    public function messages()
    {
        return [
            'section_name.required' => 'خطأ لا يمكن ترك اسم القسم فارغ.',
            'section_name.unique' => 'خطأ هذا القسم مسجل بالفعل.',
            'section_name.max' => 'خطأ يجب ان يكون اسم القسم اقل من 100 حرف.',
            'section_name.min' => 'خطأ يجب ان يكون اسم القسم اكثر من 3 احرف.',
        ];
    }
}
