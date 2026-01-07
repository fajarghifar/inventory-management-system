<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
            'name'           => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email'          => ['nullable', 'string', 'email', 'max:255', 'unique:suppliers,email'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:1000'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ];
    }
}
