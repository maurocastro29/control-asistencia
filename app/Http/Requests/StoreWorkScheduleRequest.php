<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkScheduleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('weekly_hours') && is_numeric($this->input('weekly_hours'))) {
            $this->merge([
                'weekly_minutes' => (int) round((float) $this->input('weekly_hours') * 60),
            ]);
        }
    }

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
                'unique:work_schedules,name',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'weekly_hours' => [
                'required',
                'numeric',
                'min:0.01',
                'max:168',
            ],

            'weekly_minutes' => [
                'required',
                'integer',
                'min:1',
                'max:10080',
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $days = $this->input('days', []);
            if (!is_array($days) || !$this->filled('weekly_minutes')) {
                return;
            }

            $calculatedMinutes = collect($days)->sum(function (array $day): int {
                if (!(bool) ($day['is_working_day'] ?? false)
                    || empty($day['entry_time'])
                    || empty($day['exit_time'])) {
                    return 0;
                }

                $entry = strtotime($day['entry_time']);
                $exit = strtotime($day['exit_time']);
                if ($exit < $entry) {
                    $exit += 24 * 60 * 60;
                }

                return max(0, (int) (($exit - $entry) / 60) - (int) ($day['lunch_minutes'] ?? 0));
            });

            if ((int) $this->input('weekly_minutes') !== $calculatedMinutes) {
                $validator->errors()->add(
                    'weekly_hours',
                    'Las horas semanales deben coincidir con la suma de los días laborales.'
                );
            }
        });
    }
}