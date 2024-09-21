<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class RegisterUser extends FormRequest
{
    protected function failedValidation(Validator $validator) {
        throw new ValidationException($validator);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => "required|email|min:3|max:100",
            'name' => "required|string|min:3|max:100",
            'password' => "required|min:3|max:40"
        ];
    }
}
