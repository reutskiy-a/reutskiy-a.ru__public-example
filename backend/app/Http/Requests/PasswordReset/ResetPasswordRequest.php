<?php

namespace App\Http\Requests\PasswordReset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required',
            'email' => 'required|string|email|exists:users,email',
            'token' => [
                'required',
                'string',
                Rule::exists('password_resets', 'token')->where(function ($query) {
                    $query->where('email', $this->email);
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'token.exists' => 'Неверная связка токена и email, попробуйте ещё раз'
        ];
    }

}
