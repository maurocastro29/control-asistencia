<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->where('is_active', true),
            ],

            'work_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'entry_time' => [
                'required',
                'date_format:H:i',
            ],

            'exit_time' => [
                'required',
                'date_format:H:i',
                'after:entry_time',
            ],

            'lunch_time' => [
                'required',
                'numeric',
                'min:0',
                'max:4',
            ],

            'observations' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}