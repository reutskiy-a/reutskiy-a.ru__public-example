<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:50'],
            'password' => ['sometimes', 'required_with:new_password', 'string', 'current_password'],
            'new_password' => ['sometimes', 'required_with:password', 'string', 'confirmed'],
            'new_password_confirmation' => ['sometimes', 'string']
        ];
    }
}
