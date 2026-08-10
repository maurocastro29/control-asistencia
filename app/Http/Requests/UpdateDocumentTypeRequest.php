<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentTypeRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('document_types', 'name')->ignore($this->route('document_type')),
            ],

            'abbreviation' => [
                'required',
                'string',
                'max:20',
                Rule::unique('document_types', 'abbreviation')->ignore($this->route('document_type')),
            ],

            'description' => 'nullable|string|max:255',

            'is_active' => 'required|boolean',
        ];
    }
}