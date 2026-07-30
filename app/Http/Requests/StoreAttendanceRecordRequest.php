<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRecordRequest extends FormRequest
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
    public function rules(): array {
        return [
            'employee_id' => 'required|exists:employees,id',
            'attendance_type_id' => 'required|exists:attendance_types,id',
            'attendance_datetime' => 'required|date',
            'observations' => 'nullable|string|max:255',
            'created_by' => 'required|exists:users,id',
        ];
    }
}