<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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

            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'first_last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'second_last_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'role' => [
                'required', 
                'exists:roles,name'
            ],

        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}