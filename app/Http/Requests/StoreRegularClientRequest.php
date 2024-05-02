<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegularClientRequest extends FormRequest
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
            'first_name' => 'required|max:255|min:3',
            'last_name' => 'required|max:255|min:3',
            'phone' => 'required|max:10|min:10|unique:clients,phone',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Ім`я не може бути порожнім',
            'last_name.required' => 'Фамілія не може бути порожньою',
            'phone.required' => 'Номер телефону не може бути порожнім',
            'phone.max' => 'Номер телефону має бути не більше 10 символів',
            'phone.min' => 'Номер телефону має бути не менше 10 символів',
            'phone.unique' => 'Такий номер телефону вже зареєтсрований',
        ];
    }
}
