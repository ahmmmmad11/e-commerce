<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
            'coupon' => ['required', 'string', 'max:15'],
            'type' => ['nullable', 'string', 'in:value,percent'],
            'amount' => ['required', 'numeric'],
            'end_at' => ['nullable', 'date']
        ];
    }
}
