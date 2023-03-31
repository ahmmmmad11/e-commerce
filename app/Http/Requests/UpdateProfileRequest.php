<?php

namespace App\Http\Requests;

use App\Models\Customer;
use App\Models\Seller;
use App\Rules\ValidFile;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->route()->parameter('user');
        $auth = auth()->user();

        abort_if(
            ($auth->user_type === Seller::class || $auth->user_type === Customer::class) && $auth->id !== $user->id,
            '403'
        );
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
            'profile_image' => ['nullable', 'string', 'max:255', new ValidFile()]
        ];
    }
}
