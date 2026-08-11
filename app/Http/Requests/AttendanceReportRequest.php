<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceReportRequest extends FormRequest
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
            'date_from' => [
                'nullable',
                'date',
                'before_or_equal:date_to',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],

            'employee_id' => [
                'nullable',
                'integer',
                'exists:employees,id',
            ],

            'department_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
            ],

            'position_id' => [
                'nullable',
                'integer',
                'exists:positions,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'date_from.before_or_equal' =>
                'La fecha inicial no puede ser posterior a la fecha final.',

            'date_to.after_or_equal' =>
                'La fecha final no puede ser anterior a la fecha inicial.',

            'employee_id.exists' =>
                'El empleado seleccionado no existe.',

            'department_id.exists' =>
                'El departamento seleccionado no existe.',

            'position_id.exists' =>
                'El cargo seleccionado no existe.',
        ];
    }
}