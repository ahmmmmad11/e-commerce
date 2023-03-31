<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $address = $this->route()->parameter('address');

        abort_if($address && $address->addressable_id !== auth()->user()->user_id, 403);

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
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['required_without:province', 'string', 'max:255'],
            'province' => ['required_without:state', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'neighbourhood' => ['nullable', 'string', 'max:255'],
            'st_1' => ['required', 'string', 'max:255'],
            'st_2' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
