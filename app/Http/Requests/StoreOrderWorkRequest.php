<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderWorkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'new_work_name' => 'required_without:work_id',
            'work_id' => 'required_without:new_work_name',
            'worker_id' => 'required',
            'count' => 'required|numeric',
            'price' => 'required|numeric',
            'order_id' => 'required',

        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return             [
            'new_work_name.required_without' => 'Виберіть роботу або додайте нову роботу',
            'work_id.required_without' => 'Виберіть роботу або додайте нову роботу',
        ];
    }
}
