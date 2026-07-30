<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
        return [ 'document_type_id' => 'required|exists:document_types,id',
        'department_id' => 'required|exists:departments,id',
        'position_id' => 'required|exists:positions,id',
        'document_number' => 'required|string|max:20|unique:employees,document_number',
        'first_name' => 'required|string|max:100',
        'middle_name' => 'nullable|string|max:100',
        'first_last_name' => 'required|string|max:100',
        'second_last_name' => 'nullable|string|max:100',
        'birth_date' => 'nullable|date',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:150',
        'address' => 'nullable|string|max:255',
        'hire_date' => 'required|date',
        'termination_date' => 'nullable|date|after_or_equal:hire_date',
        'is_active' => 'required|boolean', ];
    }
}