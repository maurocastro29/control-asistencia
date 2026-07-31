<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkScheduleRequest extends FormRequest
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
                Rule::unique('work_schedules', 'name')
                    ->ignore($this->route('work_schedule')),
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'days' => [
                'required',
                'array',
                'size:7',
            ],

            'days.*.week_day_id' => [
                'required',
                'exists:week_days,id',
            ],

            'days.*.entry_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'days.*.exit_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'days.*.lunch_minutes' => [
                'required',
                'integer',
                'min:0',
            ],

            'days.*.ordinary_minutes' => [
                'required',
                'integer',
                'min:0',
            ],

            'days.*.is_working_day' => [
                'required',
                'boolean',
            ],

        ];
    }
}