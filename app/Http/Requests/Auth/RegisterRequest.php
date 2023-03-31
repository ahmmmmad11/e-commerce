<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required_without:phone', 'email', 'unique:users,email'],
            'phone' => ['required_without:email', 'string', 'unique:users,phone', 'min:10', 'max:13'],
            'password' => ['required', 'string', 'confirmed']
        ];
    }
}
