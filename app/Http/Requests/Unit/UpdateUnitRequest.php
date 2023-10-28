<?php

namespace App\Http\Requests\Unit;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('units')->ignore($this->unit)
            ],
            'slug' => [
                'required',
                'alpha_dash',
                Rule::unique('units')->ignore($this->unit)
            ]
        ];
    }
}
