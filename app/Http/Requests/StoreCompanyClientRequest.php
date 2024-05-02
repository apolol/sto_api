<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyClientRequest extends FormRequest
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
            'company_name' => 'required|max:255|min:3',
            'company_address' => 'required|max:255|min:3',
            'company_edrpu' => 'required|max:255|min:3',
            'company_iban' => 'required|max:255|min:3',
            'company_ipn' => 'required|max:255|min:3',
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
            'company_name.required' => 'Назва не може бути порожнім',
            'company_address.required' => 'Фамілія не може бути порожньою',
            'company_edrpu.required' => 'Фамілія не може бути порожньою',
            'company_iban.required' => 'Фамілія не може бути порожньою',
            'company_ipn.required' => 'Фамілія не може бути порожньою',
            'phone.required' => 'Номер телефону не може бути порожнім',
            'phone.max' => 'Номер телефону має бути не більше 10 символів',
            'phone.min' => 'Номер телефону має бути не менше 10 символів',
            'phone.unique' => 'Такий номер телефону вже зареєтсрований',
        ];
    }
}
